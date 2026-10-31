<?php
namespace App\Http\Controllers\backend;

use App\Http\Controllers\Controller;
use App\Models\backend\User;
use App\Models\backend\Warehouse;
use App\Support\BranchScope;
use App\Support\WarehouseScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    // --- Web (session) ---
    public function showLogin()
    {
        return view('auth.layouts.login');
    }

    public function showRegister()
    {
        return view('auth.layouts.register');
    }

    public function register(Request $req)
    {

        $data = $req->validate([
            'name'      => ['required', 'string', 'max:150'],
            'email'     => ['nullable', 'email', 'max:191', 'unique:users,email', 'required_without_all:phone,username'],
            'phone'     => ['nullable', 'string', 'max:50', 'unique:users,phone', 'required_without_all:email,username'],
            'username'  => ['nullable', 'string', 'max:100', 'unique:users,username', 'required_without_all:email,phone'],
            'password'  => ['required', 'string', 'min:6', 'confirmed'],
            'role_id'   => ['nullable', 'integer'],
            'branch_id' => ['nullable', 'integer'],
        ], [
            'email.required_without_all'    => 'Email/Phone/Username এর যেকোনো একটি দিন।',
            'phone.required_without_all'    => 'Email/Phone/Username এর যেকোনো একটি দিন।',
            'username.required_without_all' => 'Email/Phone/Username এর যেকোনো একটি দিন।',
        ]);

        // username auto-generate (optional)
        if (empty($data['username'])) {
            $base = strtolower(preg_replace('/\s+/', '', $data['name']));
            $try  = $base;
            $i    = 1;
            while (User::where('username', $try)->exists()) {
                $try = $base . $i++;
            }
            $data['username'] = $try;
        }

        $roleId = $data['role_id'] ?? null;

        $user            = new User();
        $user->name      = $data['name'];
        $user->email     = $data['email'] ?? null;
        $user->phone     = $data['phone'] ?? null;
        $user->username  = $data['username'] ?? null;
        $user->password  = Hash::make($data['password']);
        $user->role_id   = $roleId;
        $user->branch_id = $data['branch_id'] ?? null;
        $user->status    = 1;
        $user->save();

        // রেজিস্ট্রেশন শেষে auto-login (চাইলে বন্ধ রাখতে পারেন)
        Auth::login($user);
        $req->session()->regenerate();

        return redirect()->route('backend.dashboard')->with('success', 'Registration successful!');
        // auto-login না চাইলে:
        // return redirect()->route('login')->with('success','Registration successful! Please login.');
    }


    public function login(Request $req)
    {
        // 🔐 Already logged in? redirect
        if (Auth::check()) {
            return redirect()->route('backend.dashboard');
        }

        // ✅ Validation
        $data = $req->validate([
            'identifier' => 'required|string',
            'password'   => 'required|string',
            'remember'   => 'nullable|boolean',
        ]);

        $id       = $data['identifier'];
        $pwd      = $data['password'];
        $remember = (bool) ($data['remember'] ?? false);

        // 🔍 Detect login type
        if (filter_var($id, FILTER_VALIDATE_EMAIL)) {
            $creds = ['email' => $id, 'password' => $pwd, 'status' => 1];
        } elseif (preg_match('/^\+?\d[\d\s\-()]{4,}$/', $id)) {
            $creds = ['phone' => $id, 'password' => $pwd, 'status' => 1];
        } else {
            $creds = ['username' => $id, 'password' => $pwd, 'status' => 1];
        }

        // 🔐 Login attempt
        if (! Auth::attempt($creds, $remember)) {
            return back()
                ->withErrors(['identifier' => 'Invalid credentials'])
                ->withInput();
        }

        // 🔐 Regenerate session (security)
        $req->session()->regenerate();

        $user = Auth::user();

        // ---------------------------------
        // Branch & Warehouse Context Setup
        // ---------------------------------

        // 🟢 Super Admin → access all branches
        if ($user->isSuper()) {
            BranchScope::setAll();
            return redirect()->intended(route('backend.dashboard'));
        }

        // 🏢 Set Branch Scope
        BranchScope::setBranch((int) $user->branch_id);

        // 📦 Default Warehouse
        $warehouseId = Warehouse::where('branch_id', $user->branch_id)
            ->where('is_default', 1)
            ->value('id') ?? Warehouse::where('branch_id', $user->branch_id)->value('id');

        //  No warehouse configured
        if (! $warehouseId) {

            Auth::logout();
            $req->session()->invalidate();
            $req->session()->regenerateToken();

            return back()
                ->withInput()
                ->withErrors([
                    'identifier' =>
                    'আপনার  জন্য কোনো Branch/Warehouse সেট করা নেই।
                 অনুগ্রহ করে Admin এর সাথে যোগাযোগ করুন।',
                ]);
        }

        // 📦 Set Warehouse Scope
        WarehouseScope::set((int) $warehouseId);

        return redirect()->intended(route('backend.dashboard'));
    }

    public function logout(Request $req)
    {
        Auth::logout();
        $req->session()->invalidate();
        $req->session()->regenerateToken();
        return redirect()->route('backend.login');
    }
}
