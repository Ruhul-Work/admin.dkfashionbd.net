<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\backend\Permission;
use App\Models\backend\Role;
use App\Support\PermCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::orderByDesc('is_super')->orderBy('id')->get();

        // module/group অনুযায়ী permissions
        $permissions = Permission::where('is_active', 1)
            ->orderBy('module')->orderBy('sort')->orderBy('name')
            ->get()
            ->groupBy('module');

        // role_permissions → matrix: [role_id][permission_id] => ['view'=>.., ...]
        $matrix = [];
        $rows   = DB::table('role_permissions')
            ->select('role_id', 'permission_id', 'can_view', 'can_add', 'can_edit', 'can_delete', 'can_export')
            ->get();

        foreach ($rows as $r) {
            $rid                = (int) $r->role_id;
            $pid                = (int) $r->permission_id;
            $matrix[$rid][$pid] = [
                'view'   => (int) $r->can_view,
                'add'    => (int) $r->can_add,
                'edit'   => (int) $r->can_edit,
                'delete' => (int) $r->can_delete,
                'export' => (int) $r->can_export,
            ];
        }

        // config থেকে abilities + labels
        $abilities = config('perm.abilities', ['view', 'add', 'edit', 'delete', 'export']);
        $labels    = config('perm.labels', [
            'view' => 'View', 'add' => 'Add', 'edit' => 'Edit', 'delete' => 'Delete', 'export' => 'Export',
        ]);

        return view('backend.modules.role.index', compact(
            'roles', 'permissions', 'matrix', 'abilities', 'labels'
        ));
    }

    public function save(Request $req)
    {
        // ইনপুট কাঠামো: items[role_id][permission_id][ability] = 0/1
        $items = $req->input('items', []);

        DB::transaction(function () use ($items) {
            foreach ($items as $roleId => $perms) {
                foreach ($perms as $pid => $flags) {
                    DB::table('role_permissions')->updateOrInsert(
                        ['role_id' => (int) $roleId, 'permission_id' => (int) $pid],
                        [
                            'can_view'   => (int) ($flags['view'] ?? 0),
                            'can_add'    => (int) ($flags['add'] ?? 0),
                            'can_edit'   => (int) ($flags['edit'] ?? 0),
                            'can_delete' => (int) ($flags['delete'] ?? 0),
                            'can_export' => (int) ($flags['export'] ?? 0),
                            'updated_at' => now(),
                            'created_at' => now(),
                        ]
                    );
                }
                // role cache bust
                PermCache::forgetRole((int) $roleId);
            }
        });

        return back()->with('success', 'Role permissions saved successfully.');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'     => ['required', 'string', 'max:100', Rule::unique('roles', 'name')],
            'key'      => ['nullable', 'string', 'max:100', Rule::unique('roles', 'key')],
            'is_super' => ['nullable', 'boolean'],
        ]);

        $isSuper = (bool) ($data['is_super'] ?? false);
        if ($isSuper && ! ($req->user()?->isSuper())) {
            abort(403, 'Only a super user can create a super role.');
        }

        // key না দিলে name থেকে underscore slug; conflict হলে suffix
        $key  = $data['key'] ?: Str::slug($data['name'], '_');
        $base = $key;
        $i    = 1;
        while (Role::where('key', $key)->exists()) {
            $key = $base . '_' . $i++;
        }

        $role = Role::create([
            'name'     => $data['name'],
            'key'      => $key,
            'is_super' => $isSuper,
        ]);

        if ($req->ajax() || $req->wantsJson()) {
            return response()->json([
                'ok'   => true,
                'id'   => $role->id,
                'msg'  => 'Role created successfully.',
                'role' => $role,
            ]);
        }

        return back()->with('success', 'Role created successfully.');
    }

    public function list()
    {
        return view('backend.modules.role.list');
    }

    public function listAjax(Request $request)
    {
        $columns   = ['id', 'name', 'key', 'is_super'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderCol  = $columns[$orderIdx] ?? 'id';
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        $base = Role::query()->select(['id', 'name', 'key', 'is_super']);

        $total = $base->count();

        $q = clone $base;
        if ($searchVal !== '') {
            $q->where(function ($w) use ($searchVal) {
                $w->where('name', 'like', "%{$searchVal}%")
                    ->orWhere('key', 'like', "%{$searchVal}%");
            });
        }
        $filtered = $q->count();

        $rows = $q->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        $data = [];
        foreach ($rows as $r) {
            $checkbox = '<label class="checkboxs">
            <input type="checkbox" class="row-check" data-id="' . $r->id . '">
            <span class="checkmarks"></span>
        </label>';

            $name = '<strong class="badge text-sm fw-semibold bg-dark-primary-gradient px-20 py-9 radius-4 text-white">' . $r->name . '</strong>';
            $key  = '<code>' . $r->key . '</code>';
            $type = $r->is_super
                ? '<span class="badge text-sm fw-semibold bg-dark-success-gradient px-20 py-9 radius-4 text-white">Super</span>'
                : '<span class="badge text-sm fw-semibold text-lilac-600 bg-lilac-100 px-20 py-9 radius-4 text-white">Normal</span>';

            // Actions:
            $actions = '<div class="action-table-data"><div class="edit-delete-action">
                <a class="btn btn-info p-2" href="' . route('rbac.role.matrix', $r->id) . '" title="Manage permissions">
                    <iconify-icon icon="mdi:lock" class="text-lg"></iconify-icon>
                </a>
                <a href="#" class="w-32-px h-32-px bg-success-focus text-success-main rounded-circle d-inline-flex align-items-center justify-content-center btn-role-edit"
                    data-id="' . $r->id . '" title="Edit"><iconify-icon icon="lucide:edit"></iconify-icon></a>
                <a href="#" class="w-32-px h-32-px bg-danger-focus text-danger-main rounded-circle d-inline-flex align-items-center justify-content-center btn-role-delete"
                    data-id="' . $r->id . '" title="Delete"><iconify-icon icon="mdi:delete"></iconify-icon></a>
                </div></div>';

            $data[] = [$r->id, $name, $key, $type, $actions];
        }

        return response()->json([
            'draw'                 => $draw,
            'iTotalRecords'        => $total,
            'iTotalDisplayRecords' => $filtered,
            'aaData'               => $data,
        ]);
    }

    public function matrix(Role $role)
    {
        // Super হলে read-only banner দেখানো হবে
        $permissions = Permission::where('is_active', 1)
            ->orderBy('module')->orderBy('sort')->orderBy('name')
            ->get()->groupBy('module');

        // শুধু এই রোলের permission flags আনো
        $matrix = [];
        $rows   = \DB::table('role_permissions')
            ->where('role_id', $role->id)
            ->select('permission_id', 'can_view', 'can_add', 'can_edit', 'can_delete', 'can_export')
            ->get();

        foreach ($rows as $r) {
            $pid          = (int) $r->permission_id;
            $matrix[$pid] = [
                'view'   => (int) $r->can_view, 'add'      => (int) $r->can_add, 'edit' => (int) $r->can_edit,
                'delete' => (int) $r->can_delete, 'export' => (int) $r->can_export,
            ];
        }

        $abilities = config('perm.abilities', ['view', 'add', 'edit', 'delete', 'export']);
        $labels    = config('perm.labels', [
            'view' => 'View', 'add' => 'Add', 'edit' => 'Edit', 'delete' => 'Delete', 'export' => 'Export',
        ]);

        return view('backend.modules.role.matrix_single', compact(
            'role', 'permissions', 'matrix', 'abilities', 'labels'
        ));
    }

    public function matrixSave(Request $req, Role $role)
    {
        // items[permission_id][ability] = 0/1
        $items = $req->input('items', []);

        if ($role->is_super) {
            abort(403, 'Super role is implicitly all-allowed.');
        }

        \DB::transaction(function () use ($items, $role) {
            foreach ($items as $pid => $flags) {
                \DB::table('role_permissions')->updateOrInsert(
                    ['role_id' => $role->id, 'permission_id' => (int) $pid],
                    [
                        'can_view'   => (int) ($flags['view'] ?? 0),
                        'can_add'    => (int) ($flags['add'] ?? 0),
                        'can_edit'   => (int) ($flags['edit'] ?? 0),
                        'can_delete' => (int) ($flags['delete'] ?? 0),
                        'can_export' => (int) ($flags['export'] ?? 0),
                        'updated_at' => now(), 'created_at' => now(),
                    ]
                );
            }
            PermCache::forgetRole($role->id);
        });

        return back()->with('success', 'Permissions updated for role: ' . $role->name);
    }

    public function edit(Role $role)
    {
        return response()->json([
            'id'       => $role->id,
            'name'     => $role->name,
            'key'      => $role->key,
            'is_super' => (bool) $role->is_super,
        ]);
    }

    public function update(Request $req, Role $role)
    {
        $data = $req->validate([
            'name'     => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($role->id)],
            'key'      => ['nullable', 'string', 'max:100', Rule::unique('roles', 'key')->ignore($role->id)],
            'is_super' => ['nullable', 'boolean'],
        ]);

        // শুধুমাত্র সুপার ইউজার সুপার রোল বানাতে/রাখতে পারবে
        $wantSuper = (bool) ($data['is_super'] ?? false);
        if ($wantSuper && ! ($req->user()?->isSuper())) {
            abort(403, 'Only a super user can mark a role as super.');
        }

        // key blank থাকলে পুরোনো key রাখি; না হলে slugify + unique
        $newKey = $data['key'] ?? $role->key;
        if ($newKey !== $role->key) {
            $newKey = Str::slug($newKey, '_');
            $base   = $newKey;
            $i      = 1;
            while (Role::where('key', $newKey)->where('id', '!=', $role->id)->exists()) {
                $newKey = $base . '_' . $i++;
            }
        }

        $role->name     = $data['name'];
        $role->key      = $newKey;
        $role->is_super = $wantSuper ? 1 : 0;
        $role->save();

        // cache purge দরকার হলে
        PermCache::forgetMaps();

        if ($req->ajax() || $req->wantsJson()) {
            return response()->json(['ok' => true, 'msg' => 'Role updated successfully.', 'role' => $role]);
        }
        return back()->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        // Super role delete restriction (optional)
        if ($role->is_super) {
            return response()->json([
                'ok'  => false,
                'msg' => 'Super role cannot be deleted.',
            ], 403);
        }

        $role->delete();

        return response()->json([
            'ok'  => true,
            'msg' => 'Role deleted successfully.',
        ]);
    }
}
