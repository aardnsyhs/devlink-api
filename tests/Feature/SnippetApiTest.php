<?php

use App\Models\Snippet;
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
