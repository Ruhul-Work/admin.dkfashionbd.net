<?php
require __DIR__.'/vendor/autoload.php';
$app=require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
foreach (['employee_salaries','employee_account_ledgers'] as $tbl) {
    echo "=== $tbl ===\n";
    $cols=DB::select("DESCRIBE $tbl");
    foreach($cols as $c){ echo $c->Field.' '.$c->Type.($c->Null=='YES'?' nullable':'').PHP_EOL; }
}