<?php

namespace App\Repositories;

use App\Models\Article;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleRepository implements ArticleRepositoryInterface
{
  public function __construct(private Article $model)
  {
  }

  public function getAll(array $filters = []): LengthAwarePaginator
  {
    return $this->model
      ->with(['user:id,name', 'tags'])
      ->published()
      ->when($filters['search'] ?? null, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
      ->latest('published_at')
      ->paginate($filters['per_page'] ?? 15);
  }

  public function findBySlug(string $slug): ?object
  {
    return $this->model
      ->with(['user', 'tags'])
      ->published()
      ->where('slug', $slug)
      ->firstOrFail();
  }

  public function create(array $data): object
  {
    return $this->model->create($data);
  }

  public function update(int $id, array $data): object
  {
    $article = $this->model->findOrFail($id);
    $article->update($data);
    return $article->fresh();
  }

  public function delete(int $id): bool
  {
    return $this->model->findOrFail($id)->delete();
  }

  public function findById(int $id): ?object
  {
    return $this->model->findOrFail($id);
  }
}
