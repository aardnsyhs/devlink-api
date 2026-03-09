<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
  public function __construct(private AuthService $authService)
  {
  }

  /**
   * @OA\Post(
   *     path="/api/v1/auth/register",
   *     tags={"Authentication"},
   *     summary="Register new user",
   *     @OA\RequestBody(required=true,
   *         @OA\JsonContent(
   *             required={"name","email","password"},
   *             @OA\Property(property="name", type="string"),
   *             @OA\Property(property="email", type="string", format="email"),
   *             @OA\Property(property="password", type="string", minLength=8)
   *         )
   *     ),
   *     @OA\Response(response=201, description="User registered successfully"),
   *     @OA\Response(response=422, description="Validation error")
   * )
   */
  public function register(RegisterRequest $request): JsonResponse
  {
    $result = $this->authService->register($request->validated());

    return response()->json([
      'message' => 'User registered successfully',
      'data' => $result,
    ], 201);
  }

  public function login(LoginRequest $request): JsonResponse
  {
    $result = $this->authService->login($request->validated());

    return response()->json([
      'message' => 'Login successful',
      'data' => $result,
    ]);
  }

  public function logout(): JsonResponse
  {
    $this->authService->logout(auth()->user());

    return response()->json(['message' => 'Logged out successfully']);
  }
}
