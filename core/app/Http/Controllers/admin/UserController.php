<?php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\backend\Role;
use App\Models\backend\User;
use App\Support\PermCache;
use App\Support\BranchScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $req)
    {

        return view('backend.modules.users.index');
    }

    public function listAjax(Request $request)
    {
        // DataTables params
        $columns   = ['id', 'name', 'username', 'email', 'phone', 'role_name'];
        $draw      = (int) $request->input('draw');
        $start     = (int) $request->input('start', 0);
        $length    = (int) $request->input('length', 10);
        $orderIdx  = (int) $request->input('order.0.column', 0);
        $orderDir  = strtolower($request->input('order.0.dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $searchVal = trim($request->input('search.value', ''));

        // Base query (left join roles so we can sort/filter by role name)
        $base = User::query()
            ->leftJoin('roles as r', 'r.id', '=', 'users.role_id')
            ->select([
                'users.id', 'users.name', 'users.username', 'users.email', 'users.phone',
                'users.role_id', 'r.name as role_name', 'r.is_super as role_is_super',
            ]);

        // Branch filtering
        $branchAll = BranchScope::isAll();
        $branchId = current_branch_id();

        if (!$branchAll && $branchId) {
            $base->where('users.branch_id', $branchId);
        }

        $total = (clone $base)->count();

        // Search
        if ($searchVal !== '') {
            $base->where(function ($q) use ($searchVal) {
                $q->where('users.name', 'like', "%{$searchVal}%")
                    ->orWhere('users.email', 'like', "%{$searchVal}%")
                    ->orWhere('users.phone', 'like', "%{$searchVal}%")
                    ->orWhere('users.username', 'like', "%{$searchVal}%")
                    ->orWhere('r.name', 'like', "%{$searchVal}%");
            });
        }

        $filtered = (clone $base)->count();

        // Order mapping (DT col index -> DB col)
        $orderCol = $columns[$orderIdx] ?? 'id';

        $rows = $base->orderBy($orderCol, $orderDir)
            ->skip($start)->take($length)->get();

        // Build aaData (keep index order = table headers)
        $data = [];
        foreach ($rows as $u) {
            $roleBadge = $u->role_is_super
                ? '<span class="bg-warning-focus text-warning-600 border border-warning-main px-24 py-4 radius-4 fw-medium text-sm">' . e($u->role_name ?: '—') . '</span>'
                : '<span class="bg-success-focus text-success-600 border border-success-main px-24 py-4 radius-4 fw-medium text-sm">' . e($u->role_name ?: '—') . '</span>';
            $enc     = Crypt::encryptString($u->id);
            $actions = '<div class="d-inline-flex align-items-center justify-content-end gap-1 w-100">
                            <a class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-warning-focus text-warning-main"
                                href="' . route('usermanage.userspermission.edit', $enc) . '"
                                title="Give Permission">
                                    <iconify-icon icon="ri:shield-keyhole-line"></iconify-icon>
                            </a>
                            <!-- 🔗 New: Encrypted ID in profile URL -->
                            <a href="' . route('usermanage.users.profile', ['encrypted' => $enc]) . '"
                            class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-info-focus text-info-main"
                            title="Profile">
                                <iconify-icon icon="lucide:user"></iconify-icon>
                            </a>

                            <!-- 🔽 New: Open common AjaxModal with server-rendered partial -->
                            <a href="#"
                            class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-success-focus text-success-main AjaxModal"
                            data-ajax-modal="' . route('usermanage.users.edit.modal', $u->id) . '"
                            data-size="lg"
                            data-onload="UsersIndex.onLoad"
                            data-onsuccess="UsersIndex.onSaved"
                            title="Edit">
                            <iconify-icon icon="lucide:edit"></iconify-icon>
                            </a>

                            <a href="#" class="w-32-px h-32-px rounded-circle d-inline-flex align-items-center justify-content-center bg-danger-focus text-danger-main btn-user-delete"
                            data-id="' . $u->id . '" data-url="' . route('usermanage.users.destroy', $u->id) . '" title="Delete">
                            <iconify-icon icon="mdi:delete"></iconify-icon>
                            </a>
                        </div>';

            $data[] = [
                $u->id,
                e($u->name),
                e($u->username),
                e($u->email),
                e($u->phone),
                $roleBadge,
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

    // public function store(Request $req)
    // {
    //     $data = $req->validate([
    //         'name'      => ['required', 'string', 'max:150'],
    //         'email'     => ['nullable', 'email', 'max:191', 'unique:users,email'],
    //         'username'  => ['nullable', 'string', 'max:100', 'unique:users,username'],
    //         'phone'     => ['nullable', 'string', 'max:50', 'unique:users,phone'],
    //         'password'  => ['required', 'string', 'min:6', 'confirmed'],
    //         'role_id'   => ['required', 'integer', 'exists:roles,id'],
    //         'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
    //         'status'    => ['required', 'in:0,1'],
    //     ]);

    //     $user            = new User();
    //     $user->name      = $data['name'];
    //     $user->email     = $data['email'] ?? null;
    //     $user->username  = $data['username'] ?? null;
    //     $user->phone     = $data['phone'] ?? null;
    //     $user->password  = Hash::make($data['password']);
    //     $user->role_id   = (int) $data['role_id'];
    //     $user->branch_id = $data['branch_id'] ?? null;
    //     $user->status    = (int) $data['status'];
    //     $user->meta      = null;
    //     $user->save();

    //     if ($req->ajax() || $req->wantsJson()) {
    //         return response()->json(['ok' => true, 'id' => $user->id, 'msg' => 'User created & role assigned.']);
    //     }
    //     return redirect()->route('usermanage.users.index')->with('success', 'User created & role assigned.');
    // }

    public function store(Request $req)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'email'     => ['nullable', 'email', 'max:191', 'unique:users,email'],
            'username'  => ['nullable', 'string', 'max:100', 'unique:users,username'],
            'phone'     => ['nullable', 'string', 'max:50', 'unique:users,phone'],
            'password'  => ['required', 'string', 'min:6', 'confirmed'],
            'role_id'   => ['required', 'integer', 'exists:roles,id'],
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
            'status'    => ['required', 'in:0,1'],
            'image'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ⛑ non-super হলে যে-ই আসুক, নিজের branch enforce
        if (! $req->user()?->isSuper()) {
            $data['branch_id'] = $req->user()->branch_id; // force own branch
        }
          $imagePath = null;
        if ($req->hasFile('image')) {
            $imagePath =uploadImage($req->file('image'), 'users/images');
        }

        $user            = new User();
        $user->name      = $data['name'];
        $user->email     = $data['email'] ?? null;
        $user->username  = $data['username'] ?? null;
        $user->phone     = $data['phone'] ?? null;
        $user->password  = Hash::make($data['password']);
        $user->role_id   = (int) $data['role_id'];
        $user->branch_id = $data['branch_id'] ?? null;
        $user->status    = (int) $data['status'];
        $user->image = $imagePath??null;
        $user->meta      = null;
        $user->save();

        if ($req->ajax() || $req->wantsJson()) {
            return response()->json(['ok' => true, 'id' => $user->id, 'msg' => 'User created & role assigned.']);
        }
        return redirect()->route('usermanage.users.index')->with('success', 'User created & role assigned.');
    }

    // public function show(Request $req, User $user)
    // {
    //     // Non-super user যেন super user এডিট/দেখতে না পারে
    //     if (! ($req->user()?->isSuper()) && ($user->role->is_super ?? false)) {
    //         abort(403, 'Cannot view/edit a super user.');
    //     }

    //     return response()->json([
    //         'id'        => $user->id,
    //         'name'      => $user->name,
    //         'email'     => $user->email,
    //         'username'  => $user->username,
    //         'phone'     => $user->phone,
    //         'role_id'   => $user->role_id,
    //         'role'      => $user->role?->name,
    //         'branch_id' => $user->branch_id,
    //         'status'    => (int) $user->status,
    //     ]);
    // }

    public function rolesForSelect(Request $req)
    {
        $q     = trim($req->query('q', ''));
        $roles = Role::query()
            ->when(! ($req->user()?->isSuper()), fn($qq) => $qq->where('is_super', false))
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderByDesc('is_super')->orderBy('name')
            ->limit(50)->get(['id', 'name', 'is_super']);

        return response()->json([
            'results' => $roles->map(fn($r) => [
                'id'       => $r->id,
                'text'     => $r->name . ($r->is_super ? ' (Super)' : ''),
                'is_super' => (bool) $r->is_super,
            ]),
        ]);
    }

    // public function update(Request $req, User $user)
    // {
    //     // validation: unique ignore current user
    //     $data = $req->validate([
    //         'name'      => ['required', 'string', 'max:150'],
    //         'email'     => ['nullable', 'email', 'max:191'],
    //         'username'  => ['nullable', 'string', 'max:100'],
    //         'phone'     => ['nullable', 'string', 'max:50'],
    //         'password'  => ['nullable', 'string', 'min:6', 'confirmed'],
    //         'role_id'   => ['required', 'integer'],
    //         'branch_id' => ['nullable', 'integer'],
    //         'status'    => ['required', 'in:0,1'],
    //     ]);

    //     // কমপক্ষে email/username একটাও না থাকলে ব্লক করুন
    //     if (empty($data['email']) && empty($data['username'])) {
    //         return back()->withInput()->withErrors([
    //             'email'    => 'Email বা Username—অন্তত একটি দিতে হবে।',
    //             'username' => 'Email বা Username—অন্তত একটি দিতে হবে।',
    //         ]);
    //     }

    //     // super guard: non-super ইউজার যেন super রোলে সেট করতে না পারে/সুপার ইউজার এডিট না করে
    //     $acting  = $req->user();
    //     $newRole = Role::findOrFail((int) $data['role_id']);
    //     if (! ($acting?->isSuper())) {
    //         if (($user->role->is_super ?? false) || $newRole->is_super) {
    //             abort(403, 'Insufficient permission to set/edit super role.');
    //         }
    //     }

    //     // explicit assignment
    //     $user->name     = $data['name'];
    //     $user->email    = $data['email'] ?? null;
    //     $user->username = $data['username'] ?? null;
    //     $user->phone    = $data['phone'] ?? null;
    //     if (! empty($data['password'])) {
    //         $user->password = Hash::make($data['password']);
    //     }
    //     $user->role_id   = (int) $data['role_id'];
    //     $user->branch_id = $data['branch_id'] ?? null;
    //     $user->status    = (int) $data['status'];
    //     $user->save();

    //     PermCache::forgetUser((int) $user->id);

    //     if ($req->ajax() || $req->wantsJson()) {
    //         return response()->json(['ok' => true, 'msg' => 'User updated successfully.']);
    //     }
    //     return redirect()->route('usermanage.users.index')->with('success', 'User updated successfully.');
    // }

    public function update(Request $req, User $user)
    {
        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'email'     => ['nullable', 'email', 'max:191'],
            'username'  => ['nullable', 'string', 'max:100'],
            'phone'     => ['nullable', 'string', 'max:50'],
            'password'  => ['nullable', 'string', 'min:6', 'confirmed'],
            'role_id'   => ['required', 'integer', 'exists:roles,id'],
            'branch_id' => ['nullable', 'integer',],
            'status'    => ['required', 'in:0,1'],
             'image'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // কমপক্ষে email/username একটা থাকা চাই
        if (empty($data['email']) && empty($data['username'])) {
            return response()->json([
                'message' => 'Validation error',
                'errors'  => [
                    'email'    => ['Email বা Username—অন্তত একটি দিতে হবে।'],
                    'username' => ['Email বা Username—অন্তত একটি দিতে হবে।'],
                ],
            ], 422);
        }

        // super guard
        $acting  = $req->user();
        $newRole = Role::findOrFail((int) $data['role_id']);
        if (! $acting?->isSuper()) {
            if (($user->role->is_super ?? false) || $newRole->is_super) {
                abort(403, 'Insufficient permission to set/edit super role.');
            }
            // (ঐচ্ছিক) non-super যেন অন্য branch সেট করতে না পারে:
            // $data['branch_id'] = $acting->branch_id;
        }

        $previousImage = $user->image;
    if ($req->hasFile('image')) {
        $imagePath = uploadImage($req->file('image'), 'users/images');
        $user->image = $imagePath;

        if ($previousImage && file_exists($previousImage)) {
            unlink($previousImage);
        }
    }


        // update
        $user->name     = $data['name'];
        $user->email    = $data['email'] ?? null;
        $user->username = $data['username'] ?? null;
        $user->phone    = $data['phone'] ?? null;
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->role_id   = (int) $data['role_id'];
        $user->branch_id = $data['branch_id'] ?? null;
        $user->status    = (int) $data['status'];
        $user->save();

        PermCache::forgetUser((int) $user->id);

        return response()->json(['ok' => true, 'msg' => 'User updated successfully.']);
    }

    public function destroy(Request $req, User $user)
    {
        // নিজের অ্যাকাউন্ট ডিলিট ব্লক
        if ((int) $req->user()->id === (int) $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // non-super → super ইউজার ডিলিট ব্লক
        if (! $req->user()->isSuper() && (bool) ($user->role->is_super ?? false)) {
            abort(403, 'Insufficient permission to delete a super user.');
        }

        DB::transaction(function () use ($user) {
            // soft delete user
            $user->delete();

            //  orphan clean-up: user overrides
            DB::table('user_permissions')->where('user_id', $user->id)->delete();

            // cache bust
            PermCache::forgetUser((int) $user->id);
        });
        $imagePath = $user->image;
         if (isset($imagePath) && file_exists($imagePath)) {
            unlink($imagePath);
        }
        return back()->with('success', 'User deleted successfully.');
    }

    // public function showProfile(User $user)
    // {
    //     // Fetch user with role
    //     $user = User::with('role')->findOrFail($user->id);
    //     return view('backend.modules.users.profile', compact('user'));
    // }

    public function showProfile(string $encrypted)
    {
        try {
            $id = Crypt::decryptString($encrypted);
        } catch (\Throwable $e) {
            abort(404, 'Invalid user id.');
        }

        $user = User::with('role')->findOrFail($id);
        return view('backend.modules.users.profile', compact('user'));
    }

    public function editModal(User $user, Request $request)
    {
        abort_unless($request->ajax(), 404);
        // $user->load('role:id,name');
        $user->load(['role', 'branch']);
        return view('backend.modules.users.edit_modal', compact('user'));
    }

   public function select2(Request $r)
    {
        
        $q = trim($r->input('q', ''));
        $type = $r->type;
        $base = User::query();
      

        if ($q !== '') {
            $base->where(function($x) use ($q){
                $x->where('name','like',"%{$q}%")
                ;
            });
        }

        $items = $base->orderBy('id')->orderBy('name')
                      ->limit(20)->get(['id','name']);


        return response()->json([
            'results' => $items->map(fn($t)=>[
                'id'   => $t->id,
                'text' => $t->name 
            ])
        ]);
    }

}
