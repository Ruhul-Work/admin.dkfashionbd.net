<?php
namespace App\Support;

final class WarehouseScope
{
    public static function get(): ?int
    {
        return session('warehouse_scope');
    }

    public static function set(int $id): void
    {
        session(['warehouse_scope' => $id]);
    }

    public static function clear(): void
    {
        session()->forget('warehouse_scope');
    }
}
