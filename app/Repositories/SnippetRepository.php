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
    $userId = $filters['user_id'] ?? null;

    $query = $this->model
      ->with(['user:id,name', 'tags'])
      ->when($userId, fn($q, $v) => $q->where('user_id', $v))
      ->when($filters['language'] ?? null, fn($q, $v) => $q->where('language', $v))
      ->when($filters['search'] ?? null, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
      ->when($tag, fn($q, $v) => $q->whereHas('tags', fn($tq) => $tq->where('slug', $v)));

    if ($status === 'all') {
    } elseif (in_array($status, ['draft', 'published', 'archived'], true)) {
      $status === 'published'
        ? $query->published()
        : $query->where('status', $status);
    } else {
      $query->published();
    }

    return $query
      ->latest('published_at')
      ->paginate($filters['per_page'] ?? 15);
  }

  public function findBySlug(string $slug, ?int $viewerId = null): ?object
  {
    $query = $this->model
      ->with(['user', 'tags'])
      ->where('slug', $slug);

    if ($viewerId) {
      $query->where(function ($q) use ($viewerId) {
        $q->published()
          ->orWhere('user_id', $viewerId);
      });
    } else {
      $query->published();
    }

    return $query->firstOrFail();
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
