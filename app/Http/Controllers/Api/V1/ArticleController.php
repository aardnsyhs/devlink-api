<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Jobs\IncrementArticleViews;

class ArticleController extends Controller
{
  public function show(string $slug): ArticleResource
  {
    $article = $this->articleService->getBySlug($slug);

    IncrementArticleViews::dispatch($article->id);

    return new ArticleResource($article);
  }
}
