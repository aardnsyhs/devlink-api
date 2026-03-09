<?php

use App\Models\Article;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->token = $this->user->createToken('test')->plainTextToken;
});

it('can list published articles', function () {
  Article::factory(5)->published()->create();

  $this->getJson('/api/v1/articles')
    ->assertOk()
    ->assertJsonStructure([
      'data' => [['id', 'title', 'slug', 'excerpt', 'author']],
      'meta' => ['current_page', 'total'],
    ]);
});

it('requires authentication to create article', function () {
  $this->postJson('/api/v1/articles', [])
    ->assertUnauthorized();
});

it('can create article when authenticated', function () {
  $payload = Article::factory()->make()->toArray();

  $this->withToken($this->token)
    ->postJson('/api/v1/articles', $payload)
    ->assertCreated()
    ->assertJsonPath('data.title', $payload['title']);
});
