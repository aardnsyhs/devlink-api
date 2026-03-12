<?php

use App\Models\Snippet;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
  $this->user = User::factory()->create();
  $this->token = $this->user->createToken('test')->plainTextToken;
});

it('can list only published snippets', function () {
  $initialTotal = $this->getJson('/api/v1/snippets')
    ->assertOk()
    ->json('meta.total');
  $initialTotal = (int) (is_array($initialTotal) ? end($initialTotal) : $initialTotal);

  Snippet::factory(3)->published()->create();
  Snippet::factory(2)->create(['status' => 'draft']);

  $response = $this->getJson('/api/v1/snippets')
    ->assertOk()
    ->assertJsonStructure([
      'data' => [['id', 'title', 'slug', 'language', 'author']],
      'meta' => ['current_page', 'total'],
    ]);
  $currentTotal = $response->json('meta.total');
  $currentTotal = (int) (is_array($currentTotal) ? end($currentTotal) : $currentTotal);

  expect($currentTotal)->toBe($initialTotal + 3);
});

it('does not show unpublished snippet on public endpoint', function () {
  $draftSnippet = Snippet::factory()->create(['status' => 'draft']);

  $this->getJson('/api/v1/snippets/' . $draftSnippet->slug)
    ->assertNotFound();
});

it('requires authentication to create snippet', function () {
  $this->postJson('/api/v1/snippets', [])
    ->assertUnauthorized();
});

it('can create snippet when authenticated', function () {
  $payload = [
    'title' => 'Snippet Title',
    'description' => 'Small description',
    'code' => '<?php echo "Hello";',
    'language' => 'php',
    'status' => 'published',
  ];

  $this->withToken($this->token)
    ->postJson('/api/v1/snippets', $payload)
    ->assertCreated()
    ->assertJsonPath('data.title', $payload['title'])
    ->assertJsonPath('data.language', $payload['language']);
});

it('can filter snippets by language', function () {
  $phpSnippet = Snippet::factory()->published()->create(['language' => 'php']);
  $jsSnippet = Snippet::factory()->published()->create(['language' => 'javascript']);

  $response = $this->getJson('/api/v1/snippets?language=php')
    ->assertOk();

  $ids = collect($response->json('data'))->pluck('id');
  expect($ids)->toContain($phpSnippet->id);
  expect($ids)->not->toContain($jsSnippet->id);
});

it('can filter snippets by search keyword', function () {
  $keyword = 'kw-' . uniqid();

  $matched = Snippet::factory()->published()->create([
    'title' => 'Snippet ' . $keyword,
  ]);
  $notMatched = Snippet::factory()->published()->create([
    'title' => 'Snippet without keyword',
  ]);

  $response = $this->getJson('/api/v1/snippets?search=' . $keyword)
    ->assertOk();

  $slugs = collect($response->json('data'))->pluck('slug');
  expect($slugs)->toContain($matched->slug);
  expect($slugs)->not->toContain($notMatched->slug);
});

it('can filter snippets by status', function () {
  $draftSnippet = Snippet::factory()->create(['status' => 'draft']);
  $publishedSnippet = Snippet::factory()->published()->create();

  $response = $this->getJson('/api/v1/snippets?status=draft')
    ->assertOk();

  $ids = collect($response->json('data'))->pluck('id');
  expect($ids)->toContain($draftSnippet->id);
  expect($ids)->not->toContain($publishedSnippet->id);
});

it('can filter snippets by tag slug', function () {
  $suffix = uniqid();
  $tagMatched = Tag::create(['name' => 'DevOps-' . $suffix, 'slug' => 'devops-' . $suffix]);
  $tagOther = Tag::create(['name' => 'Laravel-' . $suffix, 'slug' => 'laravel-' . $suffix]);

  $matched = Snippet::factory()->published()->create();
  $notMatched = Snippet::factory()->published()->create();

  $matched->tags()->attach($tagMatched->id);
  $notMatched->tags()->attach($tagOther->id);

  $response = $this->getJson('/api/v1/snippets?tag=' . $tagMatched->slug)
    ->assertOk();

  $ids = collect($response->json('data'))->pluck('id');
  expect($ids)->toContain($matched->id);
  expect($ids)->not->toContain($notMatched->id);
});

it('can paginate snippets with per_page parameter', function () {
  Snippet::factory(4)->published()->create();

  $response = $this->getJson('/api/v1/snippets?per_page=2')
    ->assertOk();
  $perPage = $response->json('meta.per_page');
  $perPage = (int) (is_array($perPage) ? end($perPage) : $perPage);

  expect($perPage)->toBe(2);
  expect(count($response->json('data')))->toBe(2);
});

it('can list only authenticated user snippets when mine is enabled', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();

  $ownerPublished = Snippet::factory()->published()->create(['user_id' => $owner->id]);
  $ownerDraft = Snippet::factory()->create(['user_id' => $owner->id, 'status' => 'draft']);
  $otherPublished = Snippet::factory()->published()->create(['user_id' => $otherUser->id]);

  $token = $owner->createToken('mine')->plainTextToken;

  $response = $this->withToken($token)
    ->getJson('/api/v1/snippets?mine=1&status=all')
    ->assertOk();

  $ids = collect($response->json('data'))->pluck('id');
  expect($ids)->toContain($ownerPublished->id);
  expect($ids)->toContain($ownerDraft->id);
  expect($ids)->not->toContain($otherPublished->id);
});

it('requires authentication when mine is enabled for snippets list', function () {
  $this->getJson('/api/v1/snippets?mine=1&status=all')
    ->assertUnauthorized();
});
