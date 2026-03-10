<?php

namespace App\Jobs;

use App\Models\Snippet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class IncrementSnippetViews implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(private int $snippetId)
  {
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    Snippet::where('id', $this->snippetId)->increment('views');
  }
}
