<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  public function __construct(private int $userId)
  {
  }

  /**
   * Execute the job.
   */
  public function handle(): void
  {
    $user = User::find($this->userId);

    if (! $user) {
      return;
    }

    Mail::raw(
      "Hi {$user->name}, welcome to DevLink API!",
      function ($message) use ($user) {
        $message->to($user->email)
          ->subject('Welcome to DevLink API');
      }
    );
  }
}
