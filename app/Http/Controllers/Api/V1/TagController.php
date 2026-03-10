<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\TagRequest;
use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class TagController extends Controller
{
  public function __construct(private TagService $tagService)
  {
  }

  /**
   * @OA\Get(
   *     path="/api/v1/tags",
   *     tags={"Tags"},
   *     summary="Get all tags",
   *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string")),
   *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=15)),
   *     @OA\Response(
   *         response=200,
   *         description="List of tags",
   *         @OA\JsonContent(ref="#/components/schemas/TagCollectionResponse")
   *     )
   * )
   */
  public function index(Request $request): TagCollection
  {
    $tags = $this->tagService->getAll($request->only([
      'search',
      'per_page',
    ]));

    return new TagCollection($tags);
  }

  /**
   * @OA\Get(
   *     path="/api/v1/tags/{tag}",
   *     tags={"Tags"},
   *     summary="Get tag by slug",
   *     @OA\Parameter(name="tag", in="path", required=true, @OA\Schema(type="string")),
   *     @OA\Response(
   *         response=200,
   *         description="Tag detail",
   *         @OA\JsonContent(ref="#/components/schemas/TagSingleResponse")
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Not found",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     )
   * )
   */
  public function show(string $tag): TagResource
  {
    $tag = $this->tagService->getBySlug($tag);

    return new TagResource($tag);
  }

  /**
   * @OA\Post(
   *     path="/api/v1/tags",
   *     tags={"Tags"},
   *     summary="Create new tag",
   *     security={{"bearerAuth":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/TagUpsertRequest")
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Tag created",
   *         @OA\JsonContent(ref="#/components/schemas/TagSingleResponse")
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
  public function store(TagRequest $request): JsonResponse
  {
    $tag = $this->tagService->create($request->validated());

    return (new TagResource($tag))
      ->response()
      ->setStatusCode(201);
  }

  /**
   * @OA\Put(
   *     path="/api/v1/tags/{tag}",
   *     tags={"Tags"},
   *     summary="Update tag",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="tag", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/TagUpsertRequest")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Tag updated",
   *         @OA\JsonContent(ref="#/components/schemas/TagSingleResponse")
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
  public function update(TagRequest $request, Tag $tag): TagResource
  {
    $tag = $this->tagService->update(
      $tag->id,
      $request->validated()
    );

    return new TagResource($tag);
  }

  /**
   * @OA\Delete(
   *     path="/api/v1/tags/{tag}",
   *     tags={"Tags"},
   *     summary="Delete tag",
   *     security={{"bearerAuth":{}}},
   *     @OA\Parameter(name="tag", in="path", required=true, @OA\Schema(type="integer")),
   *     @OA\Response(response=204, description="Deleted"),
   *     @OA\Response(
   *         response=401,
   *         description="Unauthenticated",
   *         @OA\JsonContent(ref="#/components/schemas/MessageResponse")
   *     )
   * )
   */
  public function destroy(Tag $tag): JsonResponse
  {
    $this->tagService->delete($tag->id);

    return response()->json(null, 204);
  }
}
