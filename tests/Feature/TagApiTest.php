<?php

use App\Models\Tag;

it('can list tags', function () {
  Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);
  Tag::create(['name' => 'PHP', 'slug' => 'php']);

  $response = $this->getJson('/api/v1/tags')
    ->assertOk()
    ->assertJsonStructure([
      'data' => [['id', 'name', 'slug']],
      'meta' => ['current_page', 'per_page', 'total', 'last_page'],
    ]);

  $total = $response->json('meta.total');
  $total = (int) (is_array($total) ? end($total) : $total);

  expect($total)->toBe(2);
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
  Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);
  Tag::create(['name' => 'PHP', 'slug' => 'php']);
  Tag::create(['name' => 'Docker', 'slug' => 'docker']);

  $response = $this->getJson('/api/v1/tags?per_page=2')
    ->assertOk();

  $perPage = $response->json('meta.per_page');
  $perPage = (int) (is_array($perPage) ? end($perPage) : $perPage);

  expect($perPage)->toBe(2);
  expect(count($response->json('data')))->toBe(2);
});

it('can show tag by slug', function () {
  $tag = Tag::create(['name' => 'Laravel', 'slug' => 'laravel']);

  $this->getJson('/api/v1/tags/' . $tag->slug)
    ->assertOk()
    ->assertJsonPath('data.slug', 'laravel');
});

it('returns not found for unknown tag slug', function () {
  $this->getJson('/api/v1/tags/not-exist')
    ->assertNotFound();
});
