<?php

namespace App\Repositories\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface TagRepositoryInterface
{
  public function getAll(array $filters = []): LengthAwarePaginator;
  public function findBySlug(string $slug): ?object;
}
