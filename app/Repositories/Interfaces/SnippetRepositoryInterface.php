<?php

namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface SnippetRepositoryInterface
{
  public function getAll(array $filters = []): LengthAwarePaginator;
  public function findById(int $id): ?object;
  public function findBySlug(string $slug): ?object;
  public function create(array $data): object;
  public function update(int $id, array $data): object;
  public function delete(int $id): bool;
}
