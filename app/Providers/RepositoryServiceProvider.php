<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ArticleRepository;
use App\Repositories\Interfaces\ArticleRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->bind(ArticleRepositoryInterface::class, ArticleRepository::class);
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    //
  }
}
