<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function ($router){
            Route::prefix('core')->group(function (){
                Route::prefix('/v1')
                ->group(base_path('routes/api/auth/auth.php'));

                Route::group([
                    'prefix' => '/v1',
                    'middleware' => 'auth:sanctum'
                ], function (){
                    $routes = glob(base_path('routes/api/**/*.php'));
                    foreach ($routes as $route) {
                       $folder = basename(dirname($route));
                       if($folder !== 'auth'){
                           require $route;
                       }
                    }
                });
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
