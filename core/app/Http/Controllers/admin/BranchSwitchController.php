<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\backend\Branch;
use App\Support\BranchScope;
use App\Models\backend\Warehouse;
use App\Support\WarehouseScope;

use Illuminate\Http\Request;


class BranchSwitchController extends Controller
{
    //  public function switch(Request $req)
    // {
    //     $user = $req->user();

    //     $data = $req->validate([
    //         'mode'      => 'required|in:all,one',
    //         'branch_id' => 'nullable|integer',
    //     ]);

    //     // super হলে যেকোনো ব্রাঞ্চ/All
    //     if ($user?->isSuper()) {
    //         if ($data['mode'] === 'all') {
    //             BranchScope::setAll();
    //         } else {
    //             $bid = (int) ($data['branch_id'] ?? 0);
    //             abort_unless(Branch::where('is_active',1)->whereKey($bid)->exists(), 422, 'Invalid branch');
    //             BranchScope::setBranch($bid);
    //         }
    //         return response()->json(['ok'=>true, 'scope'=>session('branch_scope')]);
    //     }

    //     // normal user: সবসময় নিজের ব্রাঞ্চ
    //     BranchScope::setBranch((int) $user->branch_id);
    //     return response()->json(['ok'=>true, 'scope'=>session('branch_scope')]);
    // }

     public function switch(Request $req)
    {
        $user = $req->user();

        $data = $req->validate([
            'mode'      => 'required|in:all,one',
            'branch_id' => 'nullable|integer',
        ]);

        // =========================
        // SUPER ADMIN
        // =========================
        if ($user?->isSuper()) {

            if ($data['mode'] === 'all') {
                BranchScope::setAll();
                WarehouseScope::clear(); // 🔥 all branch → warehouse irrelevant
            } else {
                $bid = (int) ($data['branch_id'] ?? 0);

                abort_unless(
                    Branch::where('is_active', 1)->whereKey($bid)->exists(),
                    422,
                    'Invalid branch'
                );

                BranchScope::setBranch($bid);

                // 🔥 AUTO-BIND WAREHOUSE
                $warehouseId = Warehouse::where('branch_id', $bid)
                    ->where('is_default', 1)
                    ->value('id')
                    ?? Warehouse::where('branch_id', $bid)->value('id');

                abort_unless($warehouseId, 422, 'No warehouse for this branch');

                WarehouseScope::set($warehouseId);
            }

            return response()->json([
                'ok' => true,
                'branch' => BranchScope::get(),
                'warehouse' => WarehouseScope::get(),
            ]);
        }

        // =========================
        // NORMAL USER
        // =========================
        $bid = (int) $user->branch_id;
        BranchScope::setBranch($bid);

        $warehouseId = Warehouse::where('branch_id', $bid)
            ->where('is_default', 1)
            ->value('id')
            ?? Warehouse::where('branch_id', $bid)->value('id');

        abort_unless($warehouseId, 422, 'No warehouse for your branch');

        WarehouseScope::set($warehouseId);

        return response()->json([
            'ok' => true,
            'branch' => BranchScope::get(),
            'warehouse' => WarehouseScope::get(),
        ]);
    }
}
