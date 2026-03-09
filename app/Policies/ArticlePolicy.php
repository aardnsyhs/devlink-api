<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
  public function viewAny(?User $user): bool
  {
    return true;
  }

  public function view(?User $user, Article $article): bool
  {
    return $article->status === 'published'
      && $article->published_at !== null;
  }

  public function create(User $user): bool
  {
    return $user->exists;
  }

  public function update(User $user, Article $article): bool
  {
    return $user->id === $article->user_id;
  }

  public function delete(User $user, Article $article): bool
  {
    return $user->id === $article->user_id;
  }
}
