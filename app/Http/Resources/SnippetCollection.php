<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SnippetCollection extends ResourceCollection
{
  public $collects = SnippetResource::class;

  /**
   * Transform the resource collection into an array.
   *
   * @return array<int|string, mixed>
   */
  public function toArray($request): array
  {
    return [
      'data' => $this->collection,
      'meta' => [
        'current_page' => $this->currentPage(),
        'per_page' => $this->perPage(),
        'total' => $this->total(),
        'last_page' => $this->lastPage(),
      ],
    ];
  }
}
