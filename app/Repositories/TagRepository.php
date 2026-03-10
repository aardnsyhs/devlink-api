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
}
