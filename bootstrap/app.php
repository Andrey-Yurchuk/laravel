<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use PHPUnit\Framework\TestCase;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $isRunningTests = defined('PHPUNIT_COMPOSER_INSTALL') 
            || class_exists(TestCase::class, false)
            || getenv('APP_ENV') === 'testing';
        
        if ($isRunningTests) {
            $middleware->validateCsrfTokens(except: ['*']);
        }

        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureRole::class,
            'subscription.active' => \App\Http\Middleware\EnsureActiveSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
