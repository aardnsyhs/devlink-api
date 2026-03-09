<?php

namespace App\Jobs;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class IncrementArticleViews implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(private int $articleId)
  {

  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    Article::where('id', $this->articleId)->increment('views');
  }
}
