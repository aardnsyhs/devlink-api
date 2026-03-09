<?php

use App\Models\Article;
use App\Models\Snippet;
use App\Models\User;

it('forbids updating article owned by another user', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();
  $token = $otherUser->createToken('test')->plainTextToken;

  $article = Article::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);

  $payload = [
    'title' => 'Updated Title',
    'excerpt' => 'Updated excerpt',
    'content' => 'Updated content',
    'status' => 'draft',
  ];

  $this->withToken($token)
    ->putJson('/api/v1/articles/' . $article->id, $payload)
    ->assertForbidden();
});

it('forbids deleting article owned by another user', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();
  $token = $otherUser->createToken('test')->plainTextToken;

  $article = Article::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);

  $this->withToken($token)
    ->deleteJson('/api/v1/articles/' . $article->id)
    ->assertForbidden();
});

it('forbids updating snippet owned by another user', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();
  $token = $otherUser->createToken('test')->plainTextToken;

  $snippet = Snippet::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);

  $payload = [
    'title' => 'Updated Snippet',
    'description' => 'Updated description',
    'code' => '<?php echo "Updated";',
    'language' => 'php',
    'status' => 'draft',
  ];

  $this->withToken($token)
    ->putJson('/api/v1/snippets/' . $snippet->id, $payload)
    ->assertForbidden();
});

it('forbids deleting snippet owned by another user', function () {
  $owner = User::factory()->create();
  $otherUser = User::factory()->create();
  $token = $otherUser->createToken('test')->plainTextToken;

  $snippet = Snippet::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);

  $this->withToken($token)
    ->deleteJson('/api/v1/snippets/' . $snippet->id)
    ->assertForbidden();
});

it('allows owner to update own article', function () {
  $owner = User::factory()->create();
  $token = $owner->createToken('test')->plainTextToken;

  $article = Article::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);

  $payload = [
    'title' => 'Owner Updated Article',
    'excerpt' => 'Owner excerpt',
    'content' => 'Owner content',
    'status' => 'draft',
  ];

  $this->withToken($token)
    ->putJson('/api/v1/articles/' . $article->id, $payload)
    ->assertOk()
    ->assertJsonPath('data.title', $payload['title']);
});

it('allows owner to delete own article', function () {
  $owner = User::factory()->create();
  $token = $owner->createToken('test')->plainTextToken;

  $article = Article::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);

  $this->withToken($token)
    ->deleteJson('/api/v1/articles/' . $article->id)
    ->assertNoContent();
});

it('allows owner to update own snippet', function () {
  $owner = User::factory()->create();
  $token = $owner->createToken('test')->plainTextToken;

  $snippet = Snippet::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);

  $payload = [
    'title' => 'Owner Updated Snippet',
    'description' => 'Owner snippet description',
    'code' => '<?php echo "Owner";',
    'language' => 'php',
    'status' => 'draft',
  ];

  $this->withToken($token)
    ->putJson('/api/v1/snippets/' . $snippet->id, $payload)
    ->assertOk()
    ->assertJsonPath('data.title', $payload['title']);
});

it('allows owner to delete own snippet', function () {
  $owner = User::factory()->create();
  $token = $owner->createToken('test')->plainTextToken;

  $snippet = Snippet::factory()->create([
    'user_id' => $owner->id,
    'status' => 'draft',
  ]);

  $this->withToken($token)
    ->deleteJson('/api/v1/snippets/' . $snippet->id)
    ->assertNoContent();
});
