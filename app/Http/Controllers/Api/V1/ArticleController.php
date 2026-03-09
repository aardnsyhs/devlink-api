<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Jobs\IncrementArticleViews;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ArticleController extends Controller
{
  public function __construct(private ArticleService $articleService)
  {
  }

  /**
   * @OA\Get(
   *     path="/api/v1/articles",
   *     tags={"Articles"},
   *     summary="Get all published articles",
   *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
   *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"draft","published","archived"})),
   *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
   *     @OA\Response(
   *         response=200,
   *         description="List of articles",
   *         @OA\JsonContent(ref="#/components/schemas/ArticleCollectionResponse")
   *     )
   * )
   */
  public function index(Request $request): ArticleCollection
  {
    $articles = $this->articleService->getAll($request->only([
      'search',
      'status',
      'per_page',
    ]));

    return new ArticleCollection($articles);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/articles/{slug}",
   *     tags={"Articles"},
   *     summary="Get article by slug",
   *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
   *     @OA\Response(
   *         response=200,
   *         description="Article detail",
   *         @OA\JsonContent(ref="#/components/schemas/ArticleSingleResponse")
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Not found",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     )
   * )
   */
  public function show(string $slug): ArticleResource
  {
    $article = $this->articleService->getBySlug($slug);
    IncrementArticleViews::dispatch($article->id);

    return new ArticleResource($article);
  }

  /**
   * @OA\Post(
   *     path="/api/v1/articles",
   *     tags={"Articles"},
   *     summary="Create new article",
   *     security={{"bearerAuth":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/ArticleUpsertRequest")
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Article created",
   *         @OA\JsonContent(ref="#/components/schemas/ArticleSingleResponse")
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error",
   *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
   *     )
   * )
   */
  public function store(ArticleRequest $request): JsonResponse
  {
    $this->authorize('create', Article::class);

    $article = $this->articleService->create(
      $request->validated(),
      $request->user()->id
    );

    return (new ArticleResource($article))
      ->response()
      ->setStatusCode(201);
  }

  /**
   * @OA\Put(
   *     path="/api/v1/articles/{id}",
   *     tags={"Articles"},
   *     summary="Update article",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/ArticleUpsertRequest")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Article updated",
   *         @OA\JsonContent(ref="#/components/schemas/ArticleSingleResponse")
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     ),
   *     @OA\Response(
   *         response=403,
   *         description="Forbidden",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error",
   *         @OA\JsonContent(ref="#/components/schemas/ValidationErrorResponse")
   *     )
   * )
   */
  public function update(ArticleRequest $request, Article $article): ArticleResource
  {
    $this->authorize('update', $article);

    $article = $this->articleService->update(
      $article->id,
      $request->validated()
    );

    return new ArticleResource($article);
  }

  /**
   * @OA\Delete(
   *     path="/api/v1/articles/{id}",
   *     tags={"Articles"},
   *     summary="Delete article",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\Response(response=204, description="Deleted"),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     ),
   *     @OA\Response(
   *         response=403,
   *         description="Forbidden",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     )
   * )
   */
  public function destroy(Article $article): JsonResponse
  {
    $this->authorize('delete', $article);
    $this->articleService->delete($article->id);

    return response()->json(null, 204);
  }
}
