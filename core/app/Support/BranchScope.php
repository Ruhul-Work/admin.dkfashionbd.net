<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;


final class BranchScope
{
    public static function get(): string|int|null
    {
        return session('branch_scope'); // 'all' | branch_id | null
    }

    public static function isAll(): bool
    {
        return session('branch_scope') === 'all';
    }

    public static function currentId(): ?int
    {
        $v = session('branch_scope');
        return is_numeric($v) ? (int) $v : null;
    }

    public static function setAll(): void
    {
        session(['branch_scope' => 'all']);
    }

    public static function setBranch(int $id): void
    {
        session(['branch_scope' => $id]);
    }

    public static function ensureDefault(): void
    {
        $user = Auth::user();
        if (! $user) return;

        if (session()->has('branch_scope')) return;

        if (method_exists($user, 'isSuper') && $user->isSuper()) {
            self::setAll();
        } else {
            self::setBranch((int) ($user->branch_id ?? 0));
        }
    }
}
