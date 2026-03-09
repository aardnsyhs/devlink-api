<?php

namespace Tests\Unit\Services;

use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Services\ArticleService;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ArticleServiceTest extends TestCase
{
  use MockeryPHPUnitIntegration;

  public function test_create_adds_user_id_and_syncs_tags(): void
  {
    $repository = Mockery::mock(ArticleRepositoryInterface::class);
    $service = new ArticleService($repository);

    $article = Mockery::mock();
    $relation = Mockery::mock();

    $article->shouldReceive('tags')->once()->andReturn($relation);
    $relation->shouldReceive('sync')->once()->with([1, 2]);
    $article->shouldReceive('load')->once()->with(['user', 'tags'])->andReturn($article);

    $repository->shouldReceive('create')
      ->once()
      ->withArgs(function (array $payload) {
        return $payload['user_id'] === 10
          && $payload['title'] === 'Test Article'
          && $payload['tags'] === [1, 2];
      })
      ->andReturn($article);

    $result = $service->create([
      'title' => 'Test Article',
      'status' => 'draft',
      'tags' => [1, 2],
    ], 10);

    $this->assertSame($article, $result);
  }

  public function test_update_sets_published_at_when_status_is_published(): void
  {
    $repository = Mockery::mock(ArticleRepositoryInterface::class);
    $service = new ArticleService($repository);

    $article = Mockery::mock();
    $relation = Mockery::mock();

    $article->shouldReceive('tags')->once()->andReturn($relation);
    $relation->shouldReceive('sync')->once()->with([3]);
    $article->shouldReceive('load')->once()->with(['user', 'tags'])->andReturn($article);

    $repository->shouldReceive('update')
      ->once()
      ->withArgs(function (int $id, array $payload) {
        return $id === 7
          && $payload['status'] === 'published'
          && array_key_exists('published_at', $payload);
      })
      ->andReturn($article);

    $result = $service->update(7, [
      'status' => 'published',
      'tags' => [3],
    ]);

    $this->assertSame($article, $result);
  }
}
