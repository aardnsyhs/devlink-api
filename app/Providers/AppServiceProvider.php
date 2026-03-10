<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Snippet;
use App\Policies\ArticlePolicy;
use App\Policies\SnippetPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    if (app()->environment('production')) {
      URL::forceScheme('https');
    }

    Gate::policy(Article::class, ArticlePolicy::class);
    Gate::policy(Snippet::class, SnippetPolicy::class);

    RateLimiter::for('api', function ($request) {
      return $request->user()
        ? Limit::perMinute(120)->by($request->user()->id)
        : Limit::perMinute(30)->by($request->ip());
    });

    RateLimiter::for('auth', function ($request) {
      return Limit::perMinute(5)->by($request->ip());
    });
  }
}
