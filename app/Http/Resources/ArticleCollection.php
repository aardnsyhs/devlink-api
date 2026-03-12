<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
  public $collects = ArticleResource::class;

  /**
   * Transform the resource collection into an array.
   *
   * @return array<int|string, mixed>
   */
  public function toArray($request): array
  {
    return [
      'data' => $this->collection,
    ];
  }

  public function paginationInformation($request, $paginated, $default): array
  {
    return [
      'meta' => [
        'current_page' => $paginated['current_page'],
        'per_page' => $paginated['per_page'],
        'total' => $paginated['total'],
        'last_page' => $paginated['last_page'],
      ],
    ];
  }
}
