<?php

namespace App\Repositories;

use App\Models\Snippet;
use App\Repositories\Interfaces\SnippetRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SnippetRepository implements SnippetRepositoryInterface
{
  public function __construct(private Snippet $model)
  {
  }

  public function getAll(array $filters = []): LengthAwarePaginator
  {
    $status = $filters['status'] ?? null;
    $tag = $filters['tag'] ?? null;

    return $this->model
      ->with(['user:id,name', 'tags'])
      ->when(
        in_array($status, ['draft', 'published', 'archived'], true),
        fn($q) => $status === 'published'
          ? $q->published()
          : $q->where('status', $status),
        fn($q) => $q->published()
      )
      ->when($filters['language'] ?? null, fn($q, $v) => $q->where('language', $v))
      ->when($filters['search'] ?? null, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
      ->when($tag, fn($q, $v) => $q->whereHas('tags', fn($tq) => $tq->where('slug', $v)))
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
    $snippet = $this->model->findOrFail($id);
    $snippet->update($data);
    return $snippet->fresh();
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
