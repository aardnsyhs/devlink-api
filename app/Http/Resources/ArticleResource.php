<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
      'excerpt' => $this->excerpt,
      'content' => $this->when($request->routeIs('articles.show'), $this->content),
      'status' => $this->status,
      'views' => $this->views,
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
