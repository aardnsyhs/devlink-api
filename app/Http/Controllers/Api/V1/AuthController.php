<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/AuthRegisterRequest")
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="User registered successfully",
   *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error",
   *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
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
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/AuthLoginRequest")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Login successful",
   *         @OA\JsonContent(ref="#/components/schemas/AuthResponse")
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Invalid credentials",
   *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
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
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     )
   * )
   */
  public function logout(): JsonResponse
  {
    $this->authService->logout(auth()->user());

    return response()->json(['message' => 'Logged out successfully']);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/me",
   *     tags={"Authentication"},
   *     summary="Get authenticated user profile",
   *     security={{"bearerAuth":{}}},
   *     @OA\Response(
   *         response=200,
   *         description="Authenticated user",
   *         @OA\JsonContent(ref="#/components/schemas/MeResponse")
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     )
   * )
   */
  public function me(Request $request): JsonResponse
  {
    return response()->json([
      'data' => [
        'user' => [
          'id' => $request->user()->id,
          'name' => $request->user()->name,
          'email' => $request->user()->email,
        ],
      ],
    ]);
  }

  /**
   * @OA\Put(
   *     path="/api/v1/me",
   *     tags={"Authentication"},
   *     summary="Update authenticated user profile",
   *     security={{"bearerAuth":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/UpdateProfileRequest")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Profile updated successfully",
   *         @OA\JsonContent(ref="#/components/schemas/MeResponse")
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error",
   *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
   *     )
   * )
   */
  public function updateProfile(UpdateProfileRequest $request): JsonResponse
  {
    $user = $this->authService->updateProfile($request->user(), $request->validated());

    return response()->json([
      'message' => 'Profile updated successfully',
      'data' => [
        'user' => [
          'id' => $user->id,
          'name' => $user->name,
          'email' => $user->email,
        ],
      ],
    ]);
  }
}
