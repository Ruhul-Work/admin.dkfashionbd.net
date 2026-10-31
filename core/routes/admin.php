<?php
use App\Http\Controllers\admin\BranchController;
use App\Http\Controllers\admin\BranchSwitchController;
use App\Http\Controllers\admin\BusinessTypeController;
use App\Http\Controllers\admin\FirewallController;
use App\Http\Controllers\admin\PermissionController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\admin\UserPermissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'perm'])->group(function () {
// ←(Perm) একবার দিলেই গ্রুপের সব রুটে অটো ability detect হবে

// ==== User Management Route====
    Route::prefix('usermanage')->name('usermanage.')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/list', [UserController::class, 'listAjax'])->name('users.list.ajax');
        // Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('users-roles', [UserController::class, 'rolesForSelect'])->name('users.roles');
        Route::get('users/{encrypted}/profile', [UserController::class, 'showProfile'])->name('users.profile');
        Route::get('users/{user}/edit-modal', [UserController::class, 'editModal'])->name('users.edit.modal');

        //=== Individual User Permission Overrides
        Route::get('users/{encrypted}/userpermission', [UserPermissionController::class, 'edit'])
            ->name('userspermission.edit');

        Route::post('users/{encrypted}/userpermission', [UserPermissionController::class, 'update'])
            ->name('userspermission.update');

         Route::get('user/all/select2', [UserController::class, 'select2'])->name('select2');

    });

// === RBAC (Role & Permission routes)===
    Route::prefix('rbac')->name('rbac.')->group(function () {

        // === RBAC (Permission routes)====
        Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store');
        Route::post('permissions/{permission}/routes', [PermissionController::class, 'attachRoute'])->name('permissions.routes.attach');
        Route::delete('permissions/{permission}/routes/{routeName}', [PermissionController::class, 'detachRoute'])->name('permissions.routes.detach');
        Route::post('permissions/list', [PermissionController::class, 'listAjax'])->name('permissions.list.ajax');
        Route::get('permissions/modules', [PermissionController::class, 'modules'])->name('permissions.modules');
        Route::get('permissions/routes/suggest', [PermissionController::class, 'routesSuggest'])->name('permissions.routes.suggest');
        Route::get('permissions/{permission}', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');

        // === RBAC (Role routes)====
        Route::get('role', [RoleController::class, 'index'])->name('role.index');
        //role matrix Save (bulk upsert)
        Route::post('role/save', [RoleController::class, 'save'])->name('role.save');
        Route::post('role', [RoleController::class, 'store'])->name('role.store');
        Route::get('role/list', [RoleController::class, 'list'])->name('role.list');
        Route::post('role/list', [RoleController::class, 'listAjax'])->name('role.list.ajax');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('role.edit');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('role.update');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('role.destroy');
        Route::get('roles/{role}/matrix', [RoleController::class, 'matrix'])->name('role.matrix');
        Route::post('roles/{role}/matrix', [RoleController::class, 'matrixSave'])->name('role.matrix.save');

    });

    Route::prefix('security')->name('security.')->group(function () {
        Route::get('firewall', [FirewallController::class, 'index'])->name('firewall.index');
        Route::post('firewall/list', [FirewallController::class, 'listAjax'])->name('firewall.list.ajax');
        Route::post('firewall', [FirewallController::class, 'store'])->name('firewall.store');
        Route::post('firewall/{rule}/toggle', [FirewallController::class, 'toggle'])->name('firewall.toggle');
        Route::delete('firewall/{rule}', [FirewallController::class, 'destroy'])->name('firewall.destroy');
    });

// Branch Management

    Route::prefix('org')->name('org.')->group(function () {

        Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
        Route::get('branches/create-modal', [BranchController::class, 'createModal'])->name('branches.createModal');
        Route::post('branches/list', [BranchController::class, 'listAjax'])->name('branches.list.ajax');
        Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
        Route::get('branches/{branch}/edit-modal', [BranchController::class, 'editModal'])->whereNumber('branch')->name('branches.editModal');
        Route::get('branches/{branch}', [BranchController::class, 'show'])->name('branches.show');
        Route::put('branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
        Route::delete('branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
        Route::post('branch/switch', [BranchSwitchController::class, 'switch'])->name('branch.switch');
        Route::get('org/branches/select2', [BranchController::class, 'select2'])->name('branches.select2');

        // Business Types (Master)
        Route::get('btypes', [BusinessTypeController::class, 'index'])->name('btypes.index');
        Route::post('btypes/list', [BusinessTypeController::class, 'listAjax'])->name('btypes.list.ajax');
        Route::get('btypes/create-modal', [BusinessTypeController::class, 'createModal'])->name('btypes.createModal');
        Route::get('btypes/{type}/edit-modal', [BusinessTypeController::class, 'editModal'])->whereNumber('type')->name('btypes.editModal');
        Route::post('btypes', [BusinessTypeController::class, 'store'])->name('btypes.store');
        Route::post('btypes/{type}', [BusinessTypeController::class, 'update'])->whereNumber('type')->name('btypes.update');
        Route::delete('btypes/{type}', [BusinessTypeController::class, 'destroy'])->whereNumber('type')->name('btypes.destroy');
        Route::get('btypes/select2', [BusinessTypeController::class, 'select2'])->name('btypes.select2');
        // Branch ↔ Types assign
        Route::get('branches/{branch}/types-modal', [BranchController::class, 'typesModal'])->whereNumber('branch')->name('branches.types.modal');
        Route::post('branches/{branch}/types-sync', [BranchController::class, 'typesSync'])->whereNumber('branch')->name('branches.types.sync');

    });

    

});
