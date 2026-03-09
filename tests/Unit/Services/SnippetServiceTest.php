<?php

namespace Tests\Unit\Services;

use App\Repositories\Interfaces\SnippetRepositoryInterface;
use App\Services\SnippetService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class SnippetServiceTest extends TestCase
{
  use MockeryPHPUnitIntegration;

  public function test_create_adds_user_id_and_syncs_tags(): void
  {
    $repository = Mockery::mock(SnippetRepositoryInterface::class);
    $service = new SnippetService($repository);

    $snippet = Mockery::mock();
    $relation = Mockery::mock();

    $snippet->shouldReceive('tags')->once()->andReturn($relation);
    $relation->shouldReceive('sync')->once()->with([2, 4]);
    $snippet->shouldReceive('load')->once()->with(['user', 'tags'])->andReturn($snippet);

    $repository->shouldReceive('create')
      ->once()
      ->withArgs(function (array $payload) {
        return $payload['user_id'] === 5
          && $payload['title'] === 'Test Snippet'
          && $payload['tags'] === [2, 4];
      })
      ->andReturn($snippet);

    $result = $service->create([
      'title' => 'Test Snippet',
      'status' => 'draft',
      'tags' => [2, 4],
    ], 5);

    $this->assertSame($snippet, $result);
  }

  public function test_update_sets_published_at_when_status_is_published(): void
  {
    $repository = Mockery::mock(SnippetRepositoryInterface::class);
    $service = new SnippetService($repository);

    $snippet = Mockery::mock();
    $relation = Mockery::mock();

    $snippet->shouldReceive('tags')->once()->andReturn($relation);
    $relation->shouldReceive('sync')->once()->with([9]);
    $snippet->shouldReceive('load')->once()->with(['user', 'tags'])->andReturn($snippet);

    $repository->shouldReceive('update')
      ->once()
      ->withArgs(function (int $id, array $payload) {
        return $id === 11
          && $payload['status'] === 'published'
          && array_key_exists('published_at', $payload);
      })
      ->andReturn($snippet);

    $result = $service->update(11, [
      'status' => 'published',
      'tags' => [9],
    ]);

    $this->assertSame($snippet, $result);
  }
}
