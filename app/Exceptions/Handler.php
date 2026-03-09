<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
  public function render($request, \Throwable $e)
  {
    if ($request->expectsJson()) {
      return match (true) {
        $e instanceof ValidationException => response()->json([
          'message' => 'Validation failed',
          'errors' => $e->errors(),
        ], 422),

        $e instanceof AuthenticationException => response()->json([
          'message' => 'Unauthenticated',
        ], 401),

        $e instanceof AuthorizationException => response()->json([
          'message' => $e->getMessage() ?: 'Forbidden',
        ], 403),

        $e instanceof NotFoundHttpException => response()->json([
          'message' => 'Resource not found',
        ], 404),

        default => response()->json([
          'message' => app()->isProduction() ? 'Server error' : $e->getMessage(),
        ], 500),
      };
    }

    return parent::render($request, $e);
  }
}
