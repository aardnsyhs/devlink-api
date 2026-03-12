<?php

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->token = $this->user->createToken('test')->plainTextToken;
});

it('can list published articles', function () {
  $initialTotal = $this->getJson('/api/v1/articles')
    ->assertOk()
    ->json('meta.total');
  $initialTotal = (int) (is_array($initialTotal) ? end($initialTotal) : $initialTotal);

  Article::factory(5)->published()->create();
  Article::factory(2)->create(['status' => 'draft']);

  $response = $this->getJson('/api/v1/articles')
    ->assertOk()
    ->assertJsonStructure([
      'data' => [['id', 'title', 'slug', 'excerpt', 'author']],
      'meta' => ['current_page', 'total'],
    ]);
  $currentTotal = $response->json('meta.total');
  $currentTotal = (int) (is_array($currentTotal) ? end($currentTotal) : $currentTotal);

  expect($currentTotal)->toBe($initialTotal + 5);
});

it('does not show unpublished article on public endpoint', function () {
  $draftArticle = Article::factory()->create(['status' => 'draft']);

  $this->getJson('/api/v1/articles/' . $draftArticle->slug)
    ->assertNotFound();
});

it('allows owner to show own unpublished article when authenticated', function () {
  $owner = User::factory()->create();
  $draftArticle = Article::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);
  $token = $owner->createToken('owner')->plainTextToken;

  $this->withToken($token)
    ->getJson('/api/v1/articles/' . $draftArticle->slug)
    ->assertOk()
    ->assertJsonPath('data.slug', $draftArticle->slug);
});

it('forbids non-owner from showing unpublished article', function () {
  $owner = User::factory()->create();
  $other = User::factory()->create();
  $draftArticle = Article::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);
  $token = $other->createToken('other')->plainTextToken;

  $this->withToken($token)
    ->getJson('/api/v1/articles/' . $draftArticle->slug)
    ->assertNotFound();
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

it('can filter articles by search keyword', function () {
  $keyword = 'kw-' . uniqid();

  $matched = Article::factory()->published()->create([
    'title' => 'Article ' . $keyword,
  ]);
  $notMatched = Article::factory()->published()->create([
    'title' => 'Article without keyword',
  ]);

  $response = $this->getJson('/api/v1/articles?search=' . $keyword)
    ->assertOk();

  $slugs = collect($response->json('data'))->pluck('slug');
  expect($slugs)->toContain($matched->slug);
  expect($slugs)->not->toContain($notMatched->slug);
});

it('can filter articles by status', function () {
  $draftArticle = Article::factory()->create(['status' => 'draft']);
  $publishedArticle = Article::factory()->published()->create();

  $response = $this->getJson('/api/v1/articles?status=draft')
    ->assertOk();

  $slugs = collect($response->json('data'))->pluck('slug');
  expect($slugs)->toContain($draftArticle->slug);
  expect($slugs)->not->toContain($publishedArticle->slug);
});

it('can filter articles by tag slug', function () {
  $suffix = uniqid();
  $tagMatched = Tag::create(['name' => 'DevOps-' . $suffix, 'slug' => 'devops-' . $suffix]);
  $tagOther = Tag::create(['name' => 'Laravel-' . $suffix, 'slug' => 'laravel-' . $suffix]);

  $matched = Article::factory()->published()->create();
  $notMatched = Article::factory()->published()->create();

  $matched->tags()->attach($tagMatched->id);
  $notMatched->tags()->attach($tagOther->id);

  $response = $this->getJson('/api/v1/articles?tag=' . $tagMatched->slug)
    ->assertOk();

  $slugs = collect($response->json('data'))->pluck('slug');
  expect($slugs)->toContain($matched->slug);
  expect($slugs)->not->toContain($notMatched->slug);
});

it('can paginate articles with per_page parameter', function () {
  Article::factory(4)->published()->create();

  $response = $this->getJson('/api/v1/articles?per_page=2')
    ->assertOk();
  $perPage = $response->json('meta.per_page');
  $perPage = (int) (is_array($perPage) ? end($perPage) : $perPage);

  expect($perPage)->toBe(2);
  expect(count($response->json('data')))->toBe(2);
});

it('can list only authenticated user articles when mine is enabled', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();

  $ownerPublished = Article::factory()->published()->create(['user_id' => $owner->id]);
  $ownerDraft = Article::factory()->create(['user_id' => $owner->id, 'status' => 'draft']);
  $otherPublished = Article::factory()->published()->create(['user_id' => $otherUser->id]);

  $token = $owner->createToken('mine')->plainTextToken;

  $response = $this->withToken($token)
    ->getJson('/api/v1/articles?mine=1&status=all')
    ->assertOk();

  $ids = collect($response->json('data'))->pluck('id');
  expect($ids)->toContain($ownerPublished->id);
  expect($ids)->toContain($ownerDraft->id);
  expect($ids)->not->toContain($otherPublished->id);
});

it('requires authentication when mine is enabled for articles list', function () {
  $this->getJson('/api/v1/articles?mine=1&status=all')
    ->assertUnauthorized();
});
