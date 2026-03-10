<?php

namespace App\Repositories;

use App\Models\Tag;
use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TagRepository implements TagRepositoryInterface
{
  public function __construct(private Tag $model)
  {
  }

  public function getAll(array $filters = []): LengthAwarePaginator
  {
    return $this->model
      ->when($filters['search'] ?? null, fn($q, $v) => $q->where('name', 'like', "%{$v}%"))
      ->orderBy('name')
      ->paginate($filters['per_page'] ?? 15);
  }

  public function findBySlug(string $slug): ?object
  {
    return $this->model
      ->where('slug', $slug)
      ->firstOrFail();
  }

  public function create(array $data): object
  {
    return $this->model->create($data);
  }

  public function update(int $id, array $data): object
  {
    $tag = $this->model->findOrFail($id);
    $tag->update($data);

    return $tag->fresh();
  }

  public function delete(int $id): bool
  {
    return $this->model->findOrFail($id)->delete();
  }

  public function findById(int $id): ?object
  {
    return $this->model->findOrFail($id);
  }

  public function existsBySlug(string $slug, ?int $ignoreId = null): bool
  {
    return $this->model
      ->where('slug', $slug)
      ->when($ignoreId !== null, fn($q) => $q->where('id', '!=', $ignoreId))
      ->exists();
  }
}
