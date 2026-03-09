<?php

namespace App\Services;

use App\Repositories\Interfaces\ArticleRepositoryInterface;

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

  public function update(int $id, array $data)
  {
    if (isset($data['status']) && $data['status'] === 'published') {
      $data['published_at'] ??= now();
    }

    $article = $this->articleRepository->update($id, $data);

    if (isset($data['tags'])) {
      $article->tags()->sync($data['tags']);
    }

    return $article->load(['user', 'tags']);
  }

  public function delete(int $id): bool
  {
    return $this->articleRepository->delete($id);
  }
}
