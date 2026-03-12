<?php

namespace App\Services;

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
  public function register(array $data): array
  {
    $user = User::create([
      'name' => $data['name'],
      'email' => $data['email'],
      'password' => Hash::make($data['password']),
    ]);

    SendWelcomeEmail::dispatch($user->id);

    $token = $user->createToken('api-token')->plainTextToken;

    return ['user' => $user, 'token' => $token];
  }

  public function login(array $data): array
  {
    $user = User::where('email', $data['email'])->first();

    if (!$user || !Hash::check($data['password'], $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['The provided credentials are incorrect.'],
      ]);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return ['user' => $user, 'token' => $token];
  }

  public function logout(User $user): void
  {
    $user->currentAccessToken()?->delete();
  }

  public function updateProfile(User $user, array $data): User
  {
    $payload = [
      'name' => $data['name'],
      'email' => $data['email'],
    ];

    if (!empty($data['password'])) {
      $payload['password'] = Hash::make($data['password']);
    }

    $user->update($payload);

    return $user->fresh();
  }
}
