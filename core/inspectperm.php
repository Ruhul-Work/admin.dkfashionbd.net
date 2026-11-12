<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "permissions sample:\n";
print_r(DB::table('permissions')->where('key','like','product%')->limit(3)->get()->toArray());
echo "product perm:\n";
print_r(DB::table('permissions')->where('key','product.products.index')->first());
echo "role 1 perms count: ".DB::table('role_permissions')->where('role_id',1)->count()."\n";
echo "max permission id: ".DB::table('permissions')->max('id')."\n";