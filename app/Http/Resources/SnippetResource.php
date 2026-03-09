<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SnippetResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray($request): array
  {
    return [
      'id' => $this->id,
      'title' => $this->title,
      'slug' => $this->slug,
      'description' => $this->description,
      'code' => $this->when($request->routeIs('snippets.show'), $this->code),
      'language' => $this->language,
      'status' => $this->status,
      'views' => $this->views,
      'likes' => $this->likes,
      'published_at' => $this->published_at?->toISOString(),
      'author' => [
        'id' => $this->user->id,
        'name' => $this->user->name,
      ],
      'tags' => TagResource::collection($this->whenLoaded('tags')),
      'created_at' => $this->created_at->toISOString(),
    ];
  }
}
