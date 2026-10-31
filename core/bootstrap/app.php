<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;



return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/core.php',
            __DIR__ . '/../routes/admin.php',
            __DIR__ . '/../routes/pos.php',
            __DIR__ . '/../routes/public.php',
        ],
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'perm'      => \App\Http\Middleware\CheckPermission::class,
            'firewall'  => \App\Http\Middleware\PosFirewall::class, //  ফায়ারওয়াল alias
            'branchscope' => \App\Http\Middleware\EnsureBranchScope::class, // ব্রাঞ্চ স্কোপ alias
            'checkbranch' => \App\Http\Middleware\CheckBranchSelected::class, // ব্রাঞ্চ সিলেক্ট চেক alias

        ]);
        // গ্লোবালি চালাতে :
        // $middleware->web(append: [\App\Http\Middleware\PosFirewall::class]);
    })
    ->withProviders([
        \App\Providers\EventServiceProvider::class, // এখানে প্রোভাইডার রেজিস্টার করুন
        
    
    ]
    )

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'The uploaded file is too large. Please upload a smaller file.',
                    'errors' => ['image' => ['The uploaded file is too large. Please upload a smaller file. max: 2MB']],
                ], 413);
            }
            return redirect()->back()->withErrors(['image' => 'The uploaded file is too large. Please upload a smaller file.']);
        });
    })->create();


  

