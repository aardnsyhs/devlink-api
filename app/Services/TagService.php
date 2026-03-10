<?php

namespace App\Services;

use App\Repositories\Interfaces\TagRepositoryInterface;

class TagService
{
  public function __construct(
    private TagRepositoryInterface $tagRepository
  ) {
  }

  public function getAll(array $filters = [])
  {
    return $this->tagRepository->getAll($filters);
  }

  public function getBySlug(string $slug)
  {
    return $this->tagRepository->findBySlug($slug);
  }
}
