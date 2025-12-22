<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . "/../routes/web.php",
        commands: __DIR__ . "/../routes/console.php",
        health: "/up",
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Handle Validation Exception
        $exceptions->render(function (
            ValidationException $exception,
            Request $request,
        ) {
            if ($request->wantsJson()) {
                return response()->json(
                    [
                        "message" => "Validation error.",
                        "response_code" => 422,
                        "data" => $exception->errors(),
                    ],
                    422,
                );
            }
        });
    })
    ->create();
