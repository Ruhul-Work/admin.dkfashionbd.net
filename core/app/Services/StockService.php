<?php
namespace App\Services;

use App\Models\backend\StockAdjustment;
use App\Models\backend\StockAdjustmentItem;
use App\Models\backend\StockCurrent;
use App\Models\backend\StockLedger;
use App\Models\backend\StockTransfer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StockService
{
    protected bool $allowNegativeInventory = false;

    /**
     * Create draft header + items (uses models)
     */
    public function createDraft(array $header, array $items, int $createdBy = null): int
    {
        return DB::transaction(function () use ($header, $items, $createdBy) {
            $now = Carbon::now();

            $adjust = StockAdjustment::create([
                'reference_no' => $header['reference_no'] ?? null,
                'branch_id'    => $header['branch_id'],
                'warehouse_id' => $header['warehouse_id'],
                'adjust_date'  => $header['adjust_date'] ?? $now->toDateTimeString(),
                'reason_code'  => $header['reason_code'] ?? null,
                'note'         => $header['note'] ?? null,
                'status'       => 'DRAFT',
                'created_by'   => $createdBy,
                'approved_by'  => $header['approved_by'] ?? null,
                'posted_at'    => null,
            ]);

            foreach ($items as $it) {
                $adjItemData = [
                    'adjustment_id' => $adjust->id,
                    'product_id'    => $it['product_id'],
                    'warehouse_id'  => $it['warehouse_id'] ?? $header['warehouse_id'] ?? null,
                    'branch_id'     => $it['branch_id'] ?? $header['branch_id'] ?? 0,
                    'direction'     => $it['direction'] ?? 'OUT',
                    'quantity'      => (float) ($it['quantity'] ?? 0),
                    'unit_cost'     => $it['unit_cost'] ?? null,
                    'note'          => $it['note'] ?? null,
                    'created_by'    => $createdBy,
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ];
                StockAdjustmentItem::create($adjItemData);
            }

            return $adjust->id;
        });
    }

    /**
     * Post (finalize) an adjustment - model based
     */
    public function postAdjustment(int $adjustmentId, int $userId): array
    {
        $now = Carbon::now();

        return DB::transaction(function () use ($adjustmentId, $userId, $now) {
            /** @var StockAdjustment $header */
            $header = StockAdjustment::where('id', $adjustmentId)->lockForUpdate()->first();
            if (! $header) {
                throw new RuntimeException("Adjustment #{$adjustmentId} not found.");
            }
            if ($header->status === 'POSTED') {
                return ['status' => 'already_posted', 'adjustment_id' => $adjustmentId];
            }
            if ($header->status === 'CANCELLED') {
                throw new RuntimeException("Adjustment #{$adjustmentId} is cancelled and cannot be posted.");
            }

            $items = $header->items()->get();
            if ($items->isEmpty()) {
                throw new RuntimeException("Adjustment #{$adjustmentId} has no items.");
            }

            foreach ($items as $item) {
                $productId   = (int) $item->product_id;
                $warehouseId = (int) ($item->warehouse_id ?? $header->warehouse_id);
                $branchId    = (int) ($item->branch_id ?? $header->branch_id ?? 0);
                $direction   = $item->direction;
                $qty         = (float) $item->quantity;
                $unitCost    = $item->unit_cost;
                $note        = $item->note;

                // ensure summary row exists using upsert (Eloquent upsert)
                $this->ensureSummaryRow($productId, $warehouseId, $branchId);

                // lock the summary row
                $summary = StockCurrent::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->where('branch_id', $branchId)
                    ->lockForUpdate()
                    ->first();

                if (! $summary) {
                    throw new RuntimeException("Failed to lock summary for product {$productId}/wh {$warehouseId}/br {$branchId}");
                }

                // validate OUT
                if ($direction === 'OUT' && ! $this->allowNegativeInventory && ($summary->quantity < $qty)) {
                    throw new RuntimeException("Insufficient stock for product {$productId} at warehouse {$warehouseId} branch {$branchId} (have {$summary->quantity}, need {$qty}).");
                }

                // insert ledger record (use model)
                StockLedger::create([
                    'txn_date'     => $now,
                    'product_id'   => $productId,
                    'warehouse_id' => $warehouseId,
                    'branch_id'    => $branchId,
                    'ref_type'     => 'ADJUSTMENT',
                    'ref_id'       => $adjustmentId,
                    'direction'    => $direction,
                    'quantity'     => $qty,
                    'unit_cost'    => $unitCost,
                    'note'         => $note,
                    'created_by'   => $userId,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                // update summary (atomic via query)
                $delta = ($direction === 'IN') ? $qty : -$qty;
                StockCurrent::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->where('branch_id', $branchId)
                    ->update([
                        'quantity' => DB::raw("quantity + ({$delta})"),
                        'version'    => DB::raw('version + 1'),
                        'updated_at' => $now,
                    ]);
            }

            // mark header posted
            $header->status      = 'POSTED';
            $header->posted_at   = $now;
            $header->approved_by = $userId;
            $header->save();

            return ['status' => 'posted', 'adjustment_id' => $adjustmentId];
        }, 5);
    }

    /**
     * Cancel (reverse) a posted adjustment - model based
     */
    public function cancelAdjustment(int $adjustmentId, int $userId): array
    {
        $now = Carbon::now();

        return DB::transaction(function () use ($adjustmentId, $userId, $now) {
            $header = StockAdjustment::where('id', $adjustmentId)->lockForUpdate()->first();
            if (! $header) {
                throw new RuntimeException("Adjustment #{$adjustmentId} not found.");
            }
            if ($header->status !== 'POSTED') {
                throw new RuntimeException("Only POSTED adjustments can be cancelled. Current status: {$header->status}");
            }

            $items = $header->items()->get();
            if ($items->isEmpty()) {
                throw new RuntimeException("Adjustment #{$adjustmentId} has no items to reverse.");
            }

            foreach ($items as $item) {
                $productId   = (int) $item->product_id;
                $warehouseId = (int) ($item->warehouse_id ?? $header->warehouse_id);
                $branchId    = (int) ($item->branch_id ?? $header->branch_id ?? 0);
                $direction   = $item->direction;
                $qty         = (float) $item->quantity;
                $unitCost    = $item->unit_cost;

                $this->ensureSummaryRow($productId, $warehouseId, $branchId);

                $summary = StockCurrent::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->where('branch_id', $branchId)
                    ->lockForUpdate()
                    ->first();

                if (! $summary) {
                    throw new RuntimeException("Failed to lock summary for reversal for product {$productId}");
                }

                $revDirection = ($direction === 'IN') ? 'OUT' : 'IN';

                StockLedger::create([
                    'txn_date'     => $now,
                    'product_id'   => $productId,
                    'warehouse_id' => $warehouseId,
                    'branch_id'    => $branchId,
                    'ref_type'     => 'ADJUSTMENT_REVERSAL',
                    'ref_id'       => $adjustmentId,
                    'direction'    => $revDirection,
                    'quantity'     => $qty,
                    'unit_cost'    => $unitCost,
                    'note'         => "Reversal of adjustment {$adjustmentId}",
                    'created_by' => $userId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $delta = ($revDirection === 'IN') ? $qty : -$qty;
                StockCurrent::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->where('branch_id', $branchId)
                    ->update([
                        'quantity' => DB::raw("quantity + ({$delta})"),
                        'version'    => DB::raw('version + 1'),
                        'updated_at' => $now,
                    ]);
            }

            $header->status = 'CANCELLED';
            $header->save();

            return ['status' => 'cancelled', 'adjustment_id' => $adjustmentId];
        });
    }

    /**
     * Ensure summary (uses upsert when available)
     */
    public function ensureSummaryRow(int $productId, int $warehouseId, int $branchId = 0): void
    {
        $now = Carbon::now();

        // Try Eloquent upsert (Laravel >= 8.45)
        try {
            StockCurrent::upsert(
                [[
                    'product_id'   => $productId,
                    'warehouse_id' => $warehouseId,
                    'branch_id'    => $branchId,
                    'quantity'     => 0.000,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]],
                ['product_id', 'warehouse_id', 'branch_id'], // unique by
                ['updated_at']                               // columns to update on duplicate
            );
            return;
        } catch (\Throwable $e) {
            // fallback to raw query if upsert not supported or fails
            DB::statement("
                INSERT INTO stock_currents (product_id, warehouse_id, branch_id, quantity, created_at, updated_at)
                VALUES (?, ?, ?, 0.000, ?, ?)
                ON DUPLICATE KEY UPDATE updated_at = VALUES(updated_at)
            ", [$productId, $warehouseId, $branchId, $now, $now]);
        }
    }

    /**
     * Rebuild from ledger - heavy op (kept raw)
     */
    public function rebuildSummaryFromLedger(bool $useSwap = true): array
    {
        $now = Carbon::now();
        return DB::transaction(function () use ($now, $useSwap) {
            $tmp = 'tmp_stock_currents_' . time();

            DB::statement("
                CREATE TABLE {$tmp} AS
                SELECT product_id, warehouse_id, COALESCE(branch_id,0) AS branch_id,
                       SUM(CASE WHEN direction='IN' THEN quantity ELSE -quantity END) AS quantity,
                       MAX(created_at) AS updated_at
                FROM stock_ledger
                GROUP BY product_id, warehouse_id, COALESCE(branch_id,0)
            ");

            if ($useSwap) {
                $backup = 'stock_currents_bkp_' . time();
                DB::statement("RENAME TABLE stock_currents TO {$backup}");
                DB::statement("CREATE TABLE stock_currents AS SELECT * FROM {$tmp}");
                DB::statement("ALTER TABLE stock_currents ADD UNIQUE KEY ux_prod_wh_branch (product_id, warehouse_id, branch_id)");
                DB::statement("ALTER TABLE stock_currents ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
                DB::statement("ALTER TABLE stock_currents ADD COLUMN version BIGINT UNSIGNED NOT NULL DEFAULT 0");
                DB::statement("DROP TABLE IF EXISTS {$tmp}");
                return ['status' => 'rebuild_swapped', 'backup_table' => $backup];
            } else {
                DB::table('stock_currents')->truncate();
                DB::statement("INSERT INTO stock_currents (product_id, warehouse_id, branch_id, quantity, updated_at) SELECT product_id, warehouse_id, branch_id, quantity, updated_at FROM {$tmp}");
                DB::statement("DROP TABLE IF EXISTS {$tmp}");
                return ['status' => 'rebuild_truncated'];
            }
        });
    }

    /**
     * Get system qty
     */
    public function getSystemQuantity(int $productId, int $warehouseId, int $branchId = 0): float
    {
        $row = StockCurrent::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('branch_id', $branchId)
            ->first();

        return $row ? (float) $row->quantity : 0.0;
    }

    /**
     * Post (finalize) a stock transfer - model based
     */

    public function postTransfer(int $transferId, int $userId)
    {
        $now = Carbon::now();

        return DB::transaction(function () use ($transferId, $userId, $now) {
            $transfer = StockTransfer::with(['items', 'fromWarehouse', 'toWarehouse'])->findOrFail($transferId);

            if (strtoupper($transfer->status) === 'POSTED') {
                throw new \Exception('Transfer already posted');
            }

            // cache warehouse branches
            $fromWh = $transfer->fromWarehouse;
            $toWh   = $transfer->toWarehouse;

            foreach ($transfer->items as $item) {
                $productId = $item->product_id;
                $qty       = (float) $item->quantity;
                $unitCost  = $item->unit_cost;

                // authoritative branch per side: warehouse branch preferred, else header fallback, else null
                $fromBranch = $fromWh->branch_id ?? $transfer->from_branch_id ?? null;
                $toBranch   = $toWh->branch_id ?? $transfer->to_branch_id ?? null;

                // 1) create OUT ledger (from warehouse)
                StockLedger::create([
                    'txn_date'     => $now,
                    'product_id'   => $productId,
                    'warehouse_id' => $transfer->from_warehouse_id,
                    'branch_id'    => $fromBranch,
                    'ref_type'     => 'TRANSFER',
                    'ref_id'       => $transfer->id,
                    'direction'    => 'OUT',
                    'quantity'     => $qty,
                    'unit_cost'    => $unitCost,
                    'note'         => 'Transfer out (transfer #' . $transfer->id . ')',
                    'created_by'   => $userId,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                // 2) create IN ledger (to warehouse)
                StockLedger::create([
                    'txn_date'     => $now,
                    'product_id'   => $productId,
                    'warehouse_id' => $transfer->to_warehouse_id,
                    'branch_id'    => $toBranch,
                    'ref_type'     => 'TRANSFER',
                    'ref_id'       => $transfer->id,
                    'direction'    => 'IN',
                    'quantity'     => $qty,
                    'unit_cost'    => $unitCost,
                    'note'         => 'Transfer in (transfer #' . $transfer->id . ')',
                    'created_by'   => $userId,
                    'created_at'   => $now,
                    'updated_at'   => $now,
                ]);

                // 3) update stock_currents safely (pessimistic lock)
                // FROM (decrement)
                $scFromQuery = StockCurrent::where('product_id', $productId)
                    ->where('warehouse_id', $transfer->from_warehouse_id);

                if ($fromBranch === null) {
                    $scFromQuery->whereNull('branch_id');
                } else {
                    $scFromQuery->where('branch_id', $fromBranch);
                }

                $scFrom = $scFromQuery->lockForUpdate()->first();

                if ($scFrom) {
                    $scFrom->quantity = $scFrom->quantity - $qty;
                    $scFrom->version  = ($scFrom->version ?? 0) + 1;
                    $scFrom->save();
                } else {
                    StockCurrent::create([
                        'product_id'   => $productId,
                        'warehouse_id' => $transfer->from_warehouse_id,
                        'branch_id'    => $fromBranch,
                        'quantity'     => -1 * $qty,
                        'version'      => 1,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }

                // TO (increment)
                $scToQuery = StockCurrent::where('product_id', $productId)
                    ->where('warehouse_id', $transfer->to_warehouse_id);

                if ($toBranch === null) {
                    $scToQuery->whereNull('branch_id');
                } else {
                    $scToQuery->where('branch_id', $toBranch);
                }

                $scTo = $scToQuery->lockForUpdate()->first();

                if ($scTo) {
                    $scTo->quantity = $scTo->quantity + $qty;
                    $scTo->version  = ($scTo->version ?? 0) + 1;
                    $scTo->save();
                } else {
                    StockCurrent::create([
                        'product_id'   => $productId,
                        'warehouse_id' => $transfer->to_warehouse_id,
                        'branch_id'    => $toBranch,
                        'quantity'     => $qty,
                        'version'      => 1,
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }
            } // end foreach items

            // finally mark transfer as posted
            $transfer->status     = 'POSTED';
            $transfer->created_at = $now;
            $transfer->created_by = $userId;
            $transfer->save();

            return ['status' => 'posted', 'transfer_id' => $transfer->id];
        });
    }
}
