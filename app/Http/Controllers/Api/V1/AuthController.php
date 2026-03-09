<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

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
   *             required={"name","email","password","password_confirmation"},
   *             @OA\Property(property="name", type="string"),
   *             @OA\Property(property="email", type="string", format="email"),
   *             @OA\Property(property="password", type="string", minLength=8),
   *             @OA\Property(property="password_confirmation", type="string", minLength=8)
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="User registered successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="User registered successfully"),
   *             @OA\Property(
   *                 property="data",
   *                 type="object",
   *                 @OA\Property(
   *                     property="user",
   *                     type="object",
   *                     @OA\Property(property="id", type="integer", example=1),
   *                     @OA\Property(property="name", type="string", example="John Doe"),
   *                     @OA\Property(property="email", type="string", format="email", example="john@example.com")
   *                 ),
   *                 @OA\Property(property="token", type="string")
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Validation failed"),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     )
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

  /**
   * @OA\Post(
   *     path="/api/v1/auth/login",
   *     tags={"Authentication"},
   *     summary="Login user",
   *     @OA\RequestBody(required=true,
   *         @OA\JsonContent(
   *             required={"email","password"},
   *             @OA\Property(property="email", type="string", format="email"),
   *             @OA\Property(property="password", type="string", minLength=8)
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Login successful",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Login successful"),
   *             @OA\Property(
   *                 property="data",
   *                 type="object",
   *                 @OA\Property(
   *                     property="user",
   *                     type="object",
   *                     @OA\Property(property="id", type="integer", example=1),
   *                     @OA\Property(property="name", type="string", example="John Doe"),
   *                     @OA\Property(property="email", type="string", format="email", example="john@example.com")
   *                 ),
   *                 @OA\Property(property="token", type="string")
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Invalid credentials",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string"),
   *             @OA\Property(property="errors", type="object")
   *         )
   *     )
   * )
   */
  public function login(LoginRequest $request): JsonResponse
  {
    $result = $this->authService->login($request->validated());

    return response()->json([
      'message' => 'Login successful',
      'data' => $result,
    ]);
  }

  /**
   * @OA\Post(
   *     path="/api/v1/auth/logout",
   *     tags={"Authentication"},
   *     summary="Logout authenticated user",
   *     security={{"bearerAuth":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Logged out successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Logged out successfully")
   *         )
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(
   *             @OA\Property(property="message", type="string", example="Unauthenticated")
   *         )
   *     )
   * )
   */
  public function logout(): JsonResponse
  {
    $this->authService->logout(auth()->user());

    return response()->json(['message' => 'Logged out successfully']);
  }
}
