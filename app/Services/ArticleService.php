<?php

namespace App\Services;

use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class ArticleService
{
  public function __construct(
    private ArticleRepositoryInterface $articleRepository
  ) {
  }

  public function getAll(array $filters = [])
  {
    return $this->articleRepository->getAll($filters);
  }

  public function getBySlug(string $slug)
  {
    return $this->articleRepository->findBySlug($slug);
  }

  public function create(array $data, int $userId)
  {
    $data['user_id'] = $userId;

    if (isset($data['status']) && $data['status'] === 'published') {
      $data['published_at'] ??= now();
    }

    $article = $this->articleRepository->create($data);

    if (!empty($data['tags'])) {
      $article->tags()->sync($data['tags']);
    }

    return $article->load(['user', 'tags']);
  }

  public function update(int $id, array $data, int $userId)
  {
    $article = $this->articleRepository->findById($id);
    $this->authorizeOwner((int) $article->user_id, $userId);

    if (isset($data['status']) && $data['status'] === 'published') {
      $data['published_at'] ??= now();
    }

    $article = $this->articleRepository->update($id, $data);

    if (isset($data['tags'])) {
      $article->tags()->sync($data['tags']);
    }

    return $article->load(['user', 'tags']);
  }

  public function delete(int $id, int $userId): bool
  {
    $article = $this->articleRepository->findById($id);
    $this->authorizeOwner((int) $article->user_id, $userId);

    return $this->articleRepository->delete($id);
  }

  private function authorizeOwner(int $ownerId, int $userId): void
  {
    if ($ownerId !== $userId) {
      throw new AuthorizationException('You are not allowed to modify this resource.');
    }
  }
}
