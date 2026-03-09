<?php

namespace App\Services;

use App\Repositories\Interfaces\SnippetRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;

class SnippetService
{
  public function __construct(
    private SnippetRepositoryInterface $snippetRepository
  ) {
  }

  public function getAll(array $filters = [])
  {
    return $this->snippetRepository->getAll($filters);
  }

  public function getBySlug(string $slug)
  {
    return $this->snippetRepository->findBySlug($slug);
  }

  public function create(array $data, int $userId)
  {
    $data['user_id'] = $userId;

    if (isset($data['status']) && $data['status'] === 'published') {
      $data['published_at'] ??= now();
    }

    $snippet = $this->snippetRepository->create($data);

    if (! empty($data['tags'])) {
      $snippet->tags()->sync($data['tags']);
    }

    return $snippet->load(['user', 'tags']);
  }

  public function update(int $id, array $data, int $userId)
  {
    $snippet = $this->snippetRepository->findById($id);
    $this->authorizeOwner((int) $snippet->user_id, $userId);

    if (isset($data['status']) && $data['status'] === 'published') {
      $data['published_at'] ??= now();
    }

    $snippet = $this->snippetRepository->update($id, $data);

    if (isset($data['tags'])) {
      $snippet->tags()->sync($data['tags']);
    }

    return $snippet->load(['user', 'tags']);
  }

  public function delete(int $id, int $userId): bool
  {
    $snippet = $this->snippetRepository->findById($id);
    $this->authorizeOwner((int) $snippet->user_id, $userId);

    return $this->snippetRepository->delete($id);
  }

  private function authorizeOwner(int $ownerId, int $userId): void
  {
    if ($ownerId !== $userId) {
      throw new AuthorizationException('You are not allowed to modify this resource.');
    }
  }
}
