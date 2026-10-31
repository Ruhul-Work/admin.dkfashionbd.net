<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\backend\Permission;
use App\Models\backend\PermissionRoute;
use App\Support\PermCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{

    public function index()
    {
        $permissions = Permission::with('routes')
            ->orderBy('module')->orderBy('sort')->get();

        return view('backend.modules.permissions.index', compact('permissions'));
    }

    public function create()
    {
        // চাইলে টাইপ অপশন/ডিফল্ট পাঠাতে পারেন
        $types = ['route' => 'Route', 'feature' => 'Feature'];
        return view('backend.modules.permissions.create', compact('types'));
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'key'       => ['required', 'max:191', Rule::unique('permissions', 'key')],
            'name'      => ['required', 'max:150'],
            'module'    => ['required', 'max:150'],
            'type'      => ['nullable', 'in:route,feature'],
            'sort'      => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'routes'    => ['nullable', 'array', 'max:1000'],
            'routes.*'  => ['string', 'max:191'],
        ]);

        $data['type']      = $data['type'] ?? 'route';
        $data['sort']      = $data['sort'] ?? 0;
        $data['is_active'] = (int) ($data['is_active'] ?? 1);

        $routes = collect($data['routes'] ?? [])->map(fn($s) => trim($s))->filter()->unique()->values()->all();
        unset($data['routes']);

        DB::transaction(function () use ($data, $routes) {
            $perm = Permission::create($data);

            if (! empty($routes)) {
                $rows = array_map(fn($r) => [
                    'permission_id' => $perm->id,
                    'route_name'    => $r,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ], $routes);
                PermissionRoute::upsert($rows, ['permission_id', 'route_name']); //upsert function accepts two parameter first one is array of rows and seocnd one is unique key like id. in this case its a composite key combination of id and name
            }
        });

        PermCache::forgetMaps();

        if ($req->ajax() || $req->wantsJson()) {
            return response()->json(['ok' => true, 'msg' => 'Permission created']);
        }
        return back()->with('success', 'Permission created');
    }

    public function listAjax(Request $request)
    {
        // DataTables params
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        // Optional filters (future): module, active
        $module = $request->input('module');
        $active = $request->input('active');

        // Base query
        $base = Permission::query()
            ->select(['id', 'module', 'name', 'key', 'is_active'])
            ->withCount('routes')
            ->with(['routes' => function ($q) {
                $q->select('permission_id', 'route_name')
                    ->orderBy('route_name')->limit(3);
            }]);

        // Total
        $total = (clone $base)->count();

        // Filters
        if ($module) {
            $base->where('module', $module);
        }

        if ($active !== null && $active !== '') {
            $base->where('is_active', (int) $active);
        }

        if ($searchVal !== '') {
            $base->where(function ($w) use ($searchVal) {
                $w->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('key', 'like', "%{$searchVal}%")
                    ->orWhere('module', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        // Order mapping (DT index -> DB column)
        $orderCol = match ($orderIdx) {
            1       => 'module',
            2       => 'name',
            3       => 'routes_count', 
            default => 'id',
        };

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        // Build aaData
        $data = [];
        foreach ($rows as $r) {
            // 1) Module badge
            $module = '<span class="badge text-sm fw-semibold bg-lilac-100 text-lilac-600 px-12 py-6 radius-4">'
            . e($r->module) . '</span>';

            // 2) Permission (name + key)
            $perm = '<strong class="fw-semibold">' . e($r->name) . '</strong><br><code>' . e($r->key) . '</code>';

            // 3) Routes chips (first 3 + +N more)
            $chips = '';
            foreach ($r->routes as $rt) {
                $chips .= '<span class="badge text-sm  border border-warning-600 text-warning-600 bg-transparent p-2 mx-2 radius-4 text-white">' . e($rt->route_name) . '</span>';
            }
            $more = max(0, ($r->routes_count - $r->routes->count()));
            if ($more > 0) {
                $chips .= '<span class="badge bg-dark-primary-gradient text-white me-1 mb-1">+' . $more . ' more</span>';
            }

            // 4) Actions
            $actions = '<div class="d-inline-flex justify-content-end gap-1 w-100">
                <a href="#" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center btn-perm-edit"
                data-id="' . $r->id . '" title="Edit"><iconify-icon icon="lucide:edit"></iconify-icon></a>
                 <a href="#" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center text-danger btn-perm-delete "
       data-id="' . $r->id . '" title="Delete"><iconify-icon icon="mdi:delete"></iconify-icon></a>
                <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center btn-perm-routes text-info"
                data-id="' . $r->id . '" title="Manage Routes"><iconify-icon icon="mdi:route"></iconify-icon></a>
            </div>';

            $data[] = [
                $r->id,
                $module,
                $perm,
                $chips,
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

    public function attachRoute(Request $req, Permission $permission)
    {
        $payload = $req->validate([
            'route_name' => ['required', 'max:191'],
        ]);

        PermissionRoute::updateOrCreate(
            ['permission_id' => $permission->id, 'route_name' => $payload['route_name']],
            []
        );

        PermCache::forgetMaps();
        return back()->with('success', 'Route attached');
    }

    public function detachRoute(Permission $permission, string $routeName)
    {
        PermissionRoute::where([
            'permission_id' => $permission->id,
            'route_name'    => $routeName,
        ])->delete();

        PermCache::forgetMaps();
        return back()->with('success', 'Route detached');
    }

// Ajax helpers for edit form
    public function modules(Request $r)
    {
        $q     = trim($r->query('q', ''));
        $items = Permission::query()
            ->when($q !== '', fn($qq) => $qq->where('module', 'like', "%{$q}%"))
            ->distinct()->orderBy('module')->limit(50)->pluck('module');
        // Select2 shape
        return response()->json(['results' => $items->map(fn($m) => ['id' => $m, 'text' => $m])]);
    }

    public function routesSuggest(Request $r)
    {
        $q   = trim($r->query('q', ''));
        $all = collect(\Illuminate\Support\Facades\Route::getRoutes())
            ->map(fn($rt) => $rt->getName())->filter(); // named only
        if ($q !== '') {
            $all = $all->filter(fn($n) => stripos($n, $q) !== false);
        }

        $all = $all->sort()->values()->take(50);
        return response()->json(['results' => $all->map(fn($n) => ['id' => $n, 'text' => $n])]);
    }

    public function edit(Permission $permission)
    {
        return response()->json([
            'id'        => $permission->id,
            'module'    => $permission->module,
            'name'      => $permission->name,
            'key'       => $permission->key,
            'sort'      => $permission->sort,
            'is_active' => (bool) $permission->is_active,
            'routes'    => $permission->routes()->orderBy('route_name')->pluck('route_name'),
        ]);
    }

    public function update(Request $req, Permission $permission)
    {
        $data = $req->validate([
            'module'    => ['sometimes', 'required', 'max:150'],
            'name'      => ['sometimes', 'required', 'max:150'],
            'key'       => ['sometimes', 'required', 'max:191', Rule::unique('permissions', 'key')->ignore($permission->id)],
            'sort'      => ['nullable', 'integer'],
            'is_active' => ['nullable', 'boolean'],
            'routes'    => ['nullable', 'array', 'max:2000'], // same field name for edit
            'routes.*'  => ['string', 'max:191'],
        ]);

        // update fields
        foreach (['module', 'name', 'key', 'sort'] as $f) {
            if (array_key_exists($f, $data)) {
                $permission->{$f} = $data[$f];
            }
        }

        if (array_key_exists('is_active', $data)) {
            $permission->is_active = (int) $data['is_active'];
        }

        $permission->save();

        // routes sync
        if ($req->has('routes')) {
            $incoming = collect($data['routes'] ?? [])->map(fn($s) => trim($s))->filter()->unique()->values()->all();
            $current  = $permission->routes()->pluck('route_name')->all();

            $attach = array_values(array_diff($incoming, $current));
            $detach = array_values(array_diff($current, $incoming));

            DB::transaction(function () use ($permission, $attach, $detach) {
                if ($attach) {
                    $rows = array_map(fn($r) => [
                        'permission_id' => $permission->id, 'route_name' => $r,
                        'created_at'    => now(), 'updated_at'           => now(),
                    ], $attach);
                    PermissionRoute::upsert($rows, ['permission_id', 'route_name']);
                }
                if ($detach) {
                    PermissionRoute::where('permission_id', $permission->id)->whereIn('route_name', $detach)->delete();
                }
            });
        }

        PermCache::forgetMaps();
        return response()->json(['ok' => true, 'msg' => 'Permission updated']);
    }

    public function destroy(Permission $permission)
    {
        // যদি এই permission কোনো role-এ ম্যাপ থাকে → ব্লক করি (সেফ)
        $inUse = DB::table('role_permissions')->where('permission_id', $permission->id)->count();
        if ($inUse > 0) {
            return response()->json([
                'ok'  => false,
                'msg' => "Cannot delete: this permission is assigned to {$inUse} role(s). Remove it from roles first.",
            ], 409);
        }

        DB::transaction(function () use ($permission) {
            // related routes detach
            PermissionRoute::where('permission_id', $permission->id)->delete();
            $permission->delete();
        });

        PermCache::forgetMaps();

        return response()->json(['ok' => true, 'msg' => 'Permission deleted']);
    }

}
