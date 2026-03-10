<?php

namespace App\Services;

use App\Repositories\Interfaces\TagRepositoryInterface;
use Illuminate\Support\Str;

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

  public function create(array $data)
  {
    $data['slug'] = $this->generateUniqueSlug($data['name']);

    return $this->tagRepository->create($data);
  }

  public function update(int $id, array $data)
  {
    $data['slug'] = $this->generateUniqueSlug($data['name'], $id);

    return $this->tagRepository->update($id, $data);
  }

  public function delete(int $id): bool
  {
    return $this->tagRepository->delete($id);
  }

  private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
  {
    $baseSlug = Str::slug($name);
    $slug = $baseSlug;
    $counter = 2;

    while ($this->slugExists($slug, $ignoreId)) {
      $slug = "{$baseSlug}-{$counter}";
      $counter++;
    }

    return $slug;
  }

  private function slugExists(string $slug, ?int $ignoreId = null): bool
  {
    return $this->tagRepository->existsBySlug($slug, $ignoreId);
  }
}
