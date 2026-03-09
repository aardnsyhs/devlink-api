<?php

use App\Jobs\SendWelcomeEmail;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('can register a new user', function () {
  $payload = [
    'name' => 'Test User',
    'email' => 'testuser@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
  ];

  $this->postJson('/api/v1/auth/register', $payload)
    ->assertCreated()
    ->assertJsonPath('message', 'User registered successfully')
    ->assertJsonStructure([
      'data' => [
        'user' => ['id', 'name', 'email'],
        'token',
      ],
    ]);
});

it('dispatches welcome email job on register', function () {
  Queue::fake();

  $payload = [
    'name' => 'Queue User',
    'email' => 'queueuser@example.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
  ];

  $this->postJson('/api/v1/auth/register', $payload)
    ->assertCreated();

  Queue::assertPushed(SendWelcomeEmail::class);
});

it('can login with valid credentials', function () {
  $user = User::factory()->create([
    'email' => 'login@example.com',
    'password' => 'password123',
  ]);

  $this->postJson('/api/v1/auth/login', [
    'email' => $user->email,
    'password' => 'password123',
  ])
    ->assertOk()
    ->assertJsonPath('message', 'Login successful')
    ->assertJsonStructure([
      'data' => [
        'user' => ['id', 'name', 'email'],
        'token',
      ],
    ]);
});

it('fails login with invalid credentials', function () {
  User::factory()->create([
    'email' => 'invalid@example.com',
    'password' => 'password123',
  ]);

  $this->postJson('/api/v1/auth/login', [
    'email' => 'invalid@example.com',
    'password' => 'wrong-password',
  ])
    ->assertStatus(422)
    ->assertJsonStructure([
      'message',
      'errors' => ['email'],
    ]);
});

it('can logout authenticated user', function () {
  $user = User::factory()->create();
  $token = $user->createToken('test')->plainTextToken;

  $this->withToken($token)
    ->postJson('/api/v1/auth/logout')
    ->assertOk()
    ->assertJsonPath('message', 'Logged out successfully');
});
