<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SnippetRequest;
use App\Http\Resources\SnippetCollection;
use App\Http\Resources\SnippetResource;
use App\Jobs\IncrementSnippetViews;
use App\Models\Snippet;
use App\Services\SnippetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class SnippetController extends Controller
{
  public function __construct(private SnippetService $snippetService)
  {
  }

  /**
   * @OA\Get(
   *     path="/api/v1/snippets",
   *     tags={"Snippets"},
   *     summary="Get all published snippets",
   *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
   *     @OA\Parameter(name="tag", in="query", @OA\Schema(type="string")),
   *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"draft","published","archived"})),
   *     @OA\Parameter(name="language", in="query", @OA\Schema(type="string")),
   *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
   *     @OA\Response(
   *         response=200,
   *         description="List of snippets",
   *         @OA\JsonContent(ref="#/components/schemas/SnippetCollectionResponse")
   *     )
   * )
   */
  public function index(Request $request): SnippetCollection
  {
    $snippets = $this->snippetService->getAll($request->only([
      'search',
      'tag',
      'status',
      'language',
      'per_page',
    ]));

    return new SnippetCollection($snippets);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/snippets/{slug}",
   *     tags={"Snippets"},
   *     summary="Get snippet by slug",
   *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
   *     @OA\Response(
   *         response=200,
   *         description="Snippet detail",
   *         @OA\JsonContent(ref="#/components/schemas/SnippetSingleResponse")
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Not found",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     )
   * )
   */
  public function show(string $slug): SnippetResource
  {
    $snippet = $this->snippetService->getBySlug($slug);
    IncrementSnippetViews::dispatch($snippet->id);

    return new SnippetResource($snippet->fresh(['user', 'tags']));
  }

  /**
   * @OA\Post(
   *     path="/api/v1/snippets",
   *     tags={"Snippets"},
   *     summary="Create new snippet",
   *     security={{"bearerAuth":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/SnippetUpsertRequest")
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Snippet created",
   *         @OA\JsonContent(ref="#/components/schemas/SnippetSingleResponse")
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
  public function store(SnippetRequest $request): JsonResponse
  {
    $this->authorize('create', Snippet::class);

    $snippet = $this->snippetService->create(
      $request->validated(),
      $request->user()->id
    );

    return (new SnippetResource($snippet))
      ->response()
      ->setStatusCode(201);
  }

  /**
   * @OA\Put(
   *     path="/api/v1/snippets/{id}",
   *     tags={"Snippets"},
   *     summary="Update snippet",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/SnippetUpsertRequest")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Snippet updated",
   *         @OA\JsonContent(ref="#/components/schemas/SnippetSingleResponse")
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
  public function update(SnippetRequest $request, Snippet $snippet): SnippetResource
  {
    $this->authorize('update', $snippet);

    $snippet = $this->snippetService->update(
      $snippet->id,
      $request->validated()
    );

    return new SnippetResource($snippet);
  }

  /**
   * @OA\Delete(
   *     path="/api/v1/snippets/{id}",
   *     tags={"Snippets"},
   *     summary="Delete snippet",
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
  public function destroy(Snippet $snippet): JsonResponse
  {
    $this->authorize('delete', $snippet);
    $this->snippetService->delete($snippet->id);

    return response()->json(null, 204);
  }
}
