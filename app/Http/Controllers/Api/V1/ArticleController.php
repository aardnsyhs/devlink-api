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
   *         @OA\JsonContent(
   *             @OA\Property(
   *                 property="data",
   *                 type="array",
   *                 @OA\Items(
   *                     type="object",
   *                     @OA\Property(property="id", type="integer"),
   *                     @OA\Property(property="title", type="string"),
   *                     @OA\Property(property="slug", type="string"),
   *                     @OA\Property(property="excerpt", type="string"),
   *                     @OA\Property(property="status", type="string", enum={"draft","published","archived"}),
   *                     @OA\Property(property="views", type="integer"),
   *                     @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
   *                     @OA\Property(
   *                         property="author",
   *                         type="object",
   *                         @OA\Property(property="id", type="integer"),
   *                         @OA\Property(property="name", type="string")
   *                     ),
   *                     @OA\Property(property="tags", type="array", @OA\Items(type="object"))
   *                 )
   *             ),
   *             @OA\Property(
   *                 property="meta",
   *                 type="object",
   *                 @OA\Property(property="current_page", type="integer"),
   *                 @OA\Property(property="per_page", type="integer"),
   *                 @OA\Property(property="total", type="integer"),
   *                 @OA\Property(property="last_page", type="integer")
   *             )
   *         )
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
   *         @OA\JsonContent(
   *             @OA\Property(property="data", type="object",
   *                 @OA\Property(property="id", type="integer"),
   *                 @OA\Property(property="title", type="string"),
   *                 @OA\Property(property="slug", type="string"),
   *                 @OA\Property(property="excerpt", type="string"),
   *                 @OA\Property(property="content", type="string"),
   *                 @OA\Property(property="status", type="string", enum={"draft","published","archived"}),
   *                 @OA\Property(property="views", type="integer"),
   *                 @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
   *                 @OA\Property(property="created_at", type="string", format="date-time"),
   *                 @OA\Property(property="author", type="object"),
   *                 @OA\Property(property="tags", type="array", @OA\Items(type="object"))
   *             )
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Not found",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Resource not found"))
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
   *     @OA\RequestBody(required=true,
   *         @OA\JsonContent(
   *             required={"title","excerpt","content"},
   *             @OA\Property(property="title", type="string"),
   *             @OA\Property(property="excerpt", type="string"),
   *             @OA\Property(property="content", type="string"),
   *             @OA\Property(property="status", type="string", enum={"draft","published","archived"}),
   *             @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
   *             @OA\Property(property="tags", type="array", @OA\Items(type="integer"))
   *         )
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Article created",
   *         @OA\JsonContent(@OA\Property(property="data", type="object"))
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error",
   *         @OA\JsonContent(@OA\Property(property="message", type="string"), @OA\Property(property="errors", type="object"))
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
   *     @OA\RequestBody(required=true,
   *         @OA\JsonContent(
   *             required={"title","excerpt","content"},
   *             @OA\Property(property="title", type="string"),
   *             @OA\Property(property="excerpt", type="string"),
   *             @OA\Property(property="content", type="string"),
   *             @OA\Property(property="status", type="string", enum={"draft","published","archived"}),
   *             @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
   *             @OA\Property(property="tags", type="array", @OA\Items(type="integer"))
   *         )
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Article updated",
   *         @OA\JsonContent(@OA\Property(property="data", type="object"))
   *     ),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
   *     ),
   *     @OA\Response(
   *         response=403,
   *         description="Forbidden",
   *         @OA\JsonContent(@OA\Property(property="message", type="string"))
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error",
   *         @OA\JsonContent(@OA\Property(property="message", type="string"), @OA\Property(property="errors", type="object"))
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
   *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
   *     ),
   *     @OA\Response(
   *         response=403,
   *         description="Forbidden",
   *         @OA\JsonContent(@OA\Property(property="message", type="string"))
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
