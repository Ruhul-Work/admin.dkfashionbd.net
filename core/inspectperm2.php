<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
echo "offers permission routes:\n";
$rows=DB::table('permission_routes')->where('route_name','like','offers%')->get();
echo $rows->count()."\n";
echo "offers perms:\n";
print_r(DB::table('permissions')->where('key','like','offer%')->get()->toArray());
echo "roles:\n";
print_r(DB::table('roles')->get()->toArray());