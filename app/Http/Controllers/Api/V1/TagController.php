<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagCollection;
use App\Http\Resources\TagResource;
use App\Services\TagService;
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
   *     path="/api/v1/tags/{slug}",
   *     tags={"Tags"},
   *     summary="Get tag by slug",
   *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string")),
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
  public function show(string $slug): TagResource
  {
    $tag = $this->tagService->getBySlug($slug);

    return new TagResource($tag);
  }
}
