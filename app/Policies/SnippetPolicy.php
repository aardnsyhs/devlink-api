<?php

namespace App\Policies;

use App\Models\Snippet;
use App\Models\User;

class SnippetPolicy
{
  public function viewAny(?User $user): bool
  {
    return true;
  }

  public function view(?User $user, Snippet $snippet): bool
  {
    return $snippet->status === 'published'
      && $snippet->published_at !== null;
  }

  public function create(User $user): bool
  {
    return $user->exists;
  }

  public function update(User $user, Snippet $snippet): bool
  {
    return $user->id === $snippet->user_id;
  }

  public function delete(User $user, Snippet $snippet): bool
  {
    return $user->id === $snippet->user_id;
  }
}
