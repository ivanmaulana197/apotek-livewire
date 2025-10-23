<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
        $middleware->redirectGuestsTo(fn(Request $request) => route('login'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, $request) {
            if (Auth::check()) {
                return redirect()->back()->with([
                    'alert' => [
                        'title'   => 'Tidak Diizinkan',
                        'message' => 'Anda tidak memiliki akses yang diperlukan.',
                        'icon'    => 'error', // Ikon SweetAlert ('success', 'error', 'warning', 'info')
                    ],
                ]);
            } else {
                session(['url.intended' => \Illuminate\Support\Facades\URL::full()]);
                return redirect()->route('login')->with([
                    'alert' => [
                        'title'   => 'Tidak Diizinkan',
                        'message' => 'Anda tidak memiliki akses yang diperlukan.',
                        'icon'    => 'error', // Ikon SweetAlert ('success', 'error', 'warning', 'info')
                    ],
                ]);
            }

        });
    })->create();