<?php

use App\Models\Article;
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

it('can paginate articles with per_page parameter', function () {
  Article::factory(4)->published()->create();

  $response = $this->getJson('/api/v1/articles?per_page=2')
    ->assertOk();
  $perPage = $response->json('meta.per_page');
  $perPage = (int) (is_array($perPage) ? end($perPage) : $perPage);

  expect($perPage)->toBe(2);
  expect(count($response->json('data')))->toBe(2);
});
