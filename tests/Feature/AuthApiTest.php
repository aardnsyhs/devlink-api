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

it('can get authenticated user profile', function () {
  $user = User::factory()->create();
  $token = $user->createToken('test')->plainTextToken;

  $this->withToken($token)
    ->getJson('/api/v1/me')
    ->assertOk()
    ->assertJsonPath('data.user.id', $user->id)
    ->assertJsonPath('data.user.email', $user->email);
});

it('requires authentication to get authenticated user profile', function () {
  $this->getJson('/api/v1/me')
    ->assertUnauthorized();
});

it('can update authenticated user profile', function () {
  $user = User::factory()->create([
    'name' => 'Old Name',
    'email' => 'old-profile@example.com',
  ]);
  $token = $user->createToken('test')->plainTextToken;

  $payload = [
    'name' => 'New Name',
    'email' => 'new-profile@example.com',
  ];

  $this->withToken($token)
    ->putJson('/api/v1/me', $payload)
    ->assertOk()
    ->assertJsonPath('message', 'Profile updated successfully')
    ->assertJsonPath('data.user.name', $payload['name'])
    ->assertJsonPath('data.user.email', $payload['email']);
});

it('can update authenticated user password', function () {
  $user = User::factory()->create([
    'password' => 'oldpassword123',
  ]);
  $token = $user->createToken('test')->plainTextToken;

  $this->withToken($token)
    ->putJson('/api/v1/me', [
      'name' => $user->name,
      'email' => $user->email,
      'password' => 'newpassword123',
      'password_confirmation' => 'newpassword123',
    ])
    ->assertOk();

  expect(\Illuminate\Support\Facades\Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
});

it('requires authentication to update authenticated user profile', function () {
  $this->putJson('/api/v1/me', [
    'name' => 'No Auth',
    'email' => 'noauth@example.com',
  ])->assertUnauthorized();
});
