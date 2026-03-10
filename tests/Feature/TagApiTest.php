<?php

use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->token = $this->user->createToken('test')->plainTextToken;
});

it('can list tags', function () {
  $initialTotal = $this->getJson('/api/v1/tags')
    ->assertOk()
    ->json('meta.total');
  $initialTotal = (int) (is_array($initialTotal) ? end($initialTotal) : $initialTotal);

  Tag::create(['name' => 'Laravel ' . uniqid(), 'slug' => 'laravel-' . uniqid()]);
  Tag::create(['name' => 'PHP ' . uniqid(), 'slug' => 'php-' . uniqid()]);

  $response = $this->getJson('/api/v1/tags')
    ->assertOk()
    ->assertJsonStructure([
      'data' => [['id', 'name', 'slug']],
      'meta' => ['current_page', 'per_page', 'total', 'last_page'],
    ]);

  $total = $response->json('meta.total');
  $total = (int) (is_array($total) ? end($total) : $total);

  expect($total)->toBe($initialTotal + 2);
});

it('can filter tags by search keyword', function () {
  $keyword = 'TagSearch' . uniqid();

  $matched = Tag::create(['name' => $keyword, 'slug' => strtolower($keyword)]);
  $notMatched = Tag::create(['name' => 'ReactTag' . uniqid(), 'slug' => 'react-' . uniqid()]);

  $response = $this->getJson('/api/v1/tags?search=' . $keyword)
    ->assertOk();

  $slugs = collect($response->json('data'))->pluck('slug');
  expect($slugs)->toContain($matched->slug);
  expect($slugs)->not->toContain($notMatched->slug);
});

it('can paginate tags with per_page parameter', function () {
  Tag::create(['name' => 'Laravel ' . uniqid(), 'slug' => 'laravel-' . uniqid()]);
  Tag::create(['name' => 'PHP ' . uniqid(), 'slug' => 'php-' . uniqid()]);
  Tag::create(['name' => 'Docker ' . uniqid(), 'slug' => 'docker-' . uniqid()]);

  $response = $this->getJson('/api/v1/tags?per_page=2')
    ->assertOk();

  $perPage = $response->json('meta.per_page');
  $perPage = (int) (is_array($perPage) ? end($perPage) : $perPage);

  expect($perPage)->toBe(2);
  expect(count($response->json('data')))->toBe(2);
});

it('can show tag by slug', function () {
  $suffix = uniqid();
  $tag = Tag::create(['name' => 'Laravel ' . $suffix, 'slug' => 'laravel-' . $suffix]);

  $this->getJson('/api/v1/tags/' . $tag->slug)
    ->assertOk()
    ->assertJsonPath('data.slug', $tag->slug);
});

it('returns not found for unknown tag slug', function () {
  $this->getJson('/api/v1/tags/not-exist')
    ->assertNotFound();
});

it('requires authentication to create tag', function () {
  $this->postJson('/api/v1/tags', [
    'name' => 'DevOps',
  ])->assertUnauthorized();
});

it('can create tag when authenticated', function () {
  $payload = [
    'name' => 'DevOps ' . uniqid(),
  ];

  $this->withToken($this->token)
    ->postJson('/api/v1/tags', $payload)
    ->assertCreated()
    ->assertJsonPath('data.name', $payload['name']);
});

it('can update tag when authenticated', function () {
  $tag = Tag::create([
    'name' => 'Initial ' . uniqid(),
    'slug' => 'initial-' . uniqid(),
  ]);

  $payload = [
    'name' => 'Updated ' . uniqid(),
  ];

  $this->withToken($this->token)
    ->putJson('/api/v1/tags/' . $tag->id, $payload)
    ->assertOk()
    ->assertJsonPath('data.name', $payload['name']);
});

it('can delete tag when authenticated', function () {
  $tag = Tag::create([
    'name' => 'Delete ' . uniqid(),
    'slug' => 'delete-' . uniqid(),
  ]);

  $this->withToken($this->token)
    ->deleteJson('/api/v1/tags/' . $tag->id)
    ->assertNoContent();
});
