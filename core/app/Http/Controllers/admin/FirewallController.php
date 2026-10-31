<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\backend\FirewallRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class FirewallController extends Controller
{
    public function index()
    {
        return view('backend.modules.firewall.index');
    }

    public function listAjax(Request $req)
    {
        $columns   = ['id', 'ip_address', 'type', 'comments', 'created_at'];
        $draw      = (int) $req->input('draw');
        $start     = (int) $req->input('start', 0);
        $length    = (int) $req->input('length', 10);
        $orderIdx  = (int) $req->input('order.0.column', 0);
        $orderDir  = strtolower($req->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($req->input('search.value', ''));

        $base = FirewallRule::query()->select(['id', 'ip_address', 'type', 'comments', 'created_at']);

        $total = (clone $base)->count();

        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('ip_address', 'like', "%{$searchVal}%")
                    ->orWhere('type', 'like', "%{$searchVal}%")
                    ->orWhere('comments', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();
        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $typeBadge = $r->isBlocked()
                ? '<span class="badge text-sm fw-semibold bg-dark-danger-gradient px-20 py-9 radius-4 text-white">Blocked</span>'
                : '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Allowed</span>';

            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center '
            . ($r->isBlocked() ? 'bg-danger-focus text-danger-main' : 'bg-success-focus text-success-main') .
            ' btn-fw-toggle" data-id="' . $r->id . '" title="' . ($r->isBlocked() ? 'Allow' : 'Block') . '">
                    <iconify-icon icon="' . ($r->isBlocked() ? 'material-symbols:block' : 'material-symbols:lock-open-right') . '"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-fw-delete" data-id="' . $r->id . '" title="Delete">
                    <iconify-icon icon="mdi:delete"></iconify-icon>
                </a>
            </div>';

            $data[] = [
                $r->id,
                e($r->ip_address),
                $typeBadge,
                e((string) $r->comments),
                e($r->created_at?->format('Y-m-d H:i')),
                $actions,
            ];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData'               => $data,
        ]);
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'ip_address' => ['required', 'string', 'max:100'],
            'type'       => ['required', Rule::in(['allow', 'block'])],
            'comments'   => ['nullable', 'string', 'max:191'],
        ]);

        // normalize ::1 => 127.0.0.1 and vice versa
        if ($data['ip_address'] === '::1') {
            $data['ip_address'] = '127.0.0.1';
        }

        $rule = FirewallRule::updateOrCreate(
            ['ip_address' => $data['ip_address']],
            ['type' => $data['type'], 'comments' => $data['comments']]
        );

        $this->purgeFwCaches($rule->ip_address);

        return response()->json(['ok' => true, 'msg' => 'Rule saved', 'rule' => $rule]);
    }

    public function toggle(FirewallRule $rule)
    {
        $rule->type = $rule->isBlocked() ? 'allow' : 'block';
        $rule->save();

        $this->purgeFwCaches($rule->ip_address);

        return response()->json(['ok' => true, 'msg' => 'Rule updated']);
    }

    public function destroy(FirewallRule $rule)
    {
        $ip = $rule->ip_address;
        $rule->delete();
        $this->purgeFwCaches($ip);
        return response()->json(['ok' => true, 'msg' => 'Rule deleted']);
    }

    // ===== Helpers =====
    private function purgeFwCaches(string $ip): void
    {
        $variants = ($ip === '127.0.0.1') ? ['127.0.0.1', '::1'] : (($ip === '::1') ? ['::1', '127.0.0.1'] : [$ip]);
        foreach ($variants as $v) {
            Cache::forget("fw:allow:$v");
            Cache::forget("fw:block:$v");
            Cache::forget("login:fail:ip:$v");
        }
    }
}
