<?php

namespace Tests\Unit\Repositories;

use App\Models\Article;
use App\Repositories\ArticleRepository;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

class ArticleRepositoryTest extends TestCase
{
  use MockeryPHPUnitIntegration;

  public function test_create_calls_model_create(): void
  {
    $model = Mockery::mock(Article::class);
    $repository = new ArticleRepository($model);

    $record = (object) ['id' => 1];
    $payload = ['title' => 'Article'];

    $model->shouldReceive('create')->once()->with($payload)->andReturn($record);

    $result = $repository->create($payload);

    $this->assertSame($record, $result);
  }

  public function test_update_calls_find_update_and_fresh(): void
  {
    $model = Mockery::mock(Article::class);
    $repository = new ArticleRepository($model);

    $found = Mockery::mock();
    $fresh = (object) ['id' => 7, 'title' => 'Updated'];
    $payload = ['title' => 'Updated'];

    $model->shouldReceive('findOrFail')->once()->with(7)->andReturn($found);
    $found->shouldReceive('update')->once()->with($payload);
    $found->shouldReceive('fresh')->once()->andReturn($fresh);

    $result = $repository->update(7, $payload);

    $this->assertSame($fresh, $result);
  }

  public function test_delete_calls_find_and_delete(): void
  {
    $model = Mockery::mock(Article::class);
    $repository = new ArticleRepository($model);

    $found = Mockery::mock();

    $model->shouldReceive('findOrFail')->once()->with(3)->andReturn($found);
    $found->shouldReceive('delete')->once()->andReturn(true);

    $this->assertTrue($repository->delete(3));
  }

  public function test_find_by_id_calls_find_or_fail(): void
  {
    $model = Mockery::mock(Article::class);
    $repository = new ArticleRepository($model);

    $record = (object) ['id' => 9];

    $model->shouldReceive('findOrFail')->once()->with(9)->andReturn($record);

    $result = $repository->findById(9);

    $this->assertSame($record, $result);
  }
}
