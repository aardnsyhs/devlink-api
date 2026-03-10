<?php

namespace App\Providers;

use App\Repositories\ArticleRepository;
use App\Repositories\SnippetRepository;
use App\Repositories\TagRepository;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\Interfaces\SnippetRepositoryInterface;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
    $this->app->bind(SnippetRepositoryInterface::class, SnippetRepository::class);
    $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    //
  }
}
