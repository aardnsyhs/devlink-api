<?php

namespace App\OpenApi;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *   schema="UserSummary",
 *   type="object",
 *   required={"id","name","email"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="John Doe"),
 *   @OA\Property(property="email", type="string", format="email", example="john@example.com")
 * )
 *
 * @OA\Schema(
 *   schema="AuthorSummary",
 *   type="object",
 *   required={"id","name"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="John Doe")
 * )
 *
 * @OA\Schema(
 *   schema="TagResource",
 *   type="object",
 *   required={"id","name","slug"},
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Laravel"),
 *   @OA\Property(property="slug", type="string", example="laravel")
 * )
 *
 * @OA\Schema(
 *   schema="TagCollectionResponse",
 *   type="object",
 *   required={"data","meta"},
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/TagResource")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *   schema="TagSingleResponse",
 *   type="object",
 *   required={"data"},
 *   @OA\Property(property="data", ref="#/components/schemas/TagResource")
 * )
 *
 * @OA\Schema(
 *   schema="PaginationMeta",
 *   type="object",
 *   required={"current_page","per_page","total","last_page"},
 *   @OA\Property(property="current_page", type="integer", example=1),
 *   @OA\Property(property="per_page", type="integer", example=15),
 *   @OA\Property(property="total", type="integer", example=100),
 *   @OA\Property(property="last_page", type="integer", example=7)
 * )
 *
 * @OA\Schema(
 *   schema="ArticleSummary",
 *   type="object",
 *   required={"id","title","slug","excerpt","status","views","author","tags","created_at"},
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="slug", type="string"),
 *   @OA\Property(property="excerpt", type="string"),
 *   @OA\Property(property="status", type="string", enum={"draft","published","archived"}),
 *   @OA\Property(property="views", type="integer"),
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="author", ref="#/components/schemas/AuthorSummary"),
 *   @OA\Property(property="tags", type="array", @OA\Items(ref="#/components/schemas/TagResource")),
 *   @OA\Property(property="created_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="ArticleDetail",
 *   allOf={
 *     @OA\Schema(ref="#/components/schemas/ArticleSummary"),
 *     @OA\Schema(
 *       @OA\Property(property="content", type="string")
 *     )
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="ArticleCollectionResponse",
 *   type="object",
 *   required={"data","meta"},
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ArticleSummary")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *   schema="ArticleSingleResponse",
 *   type="object",
 *   required={"data"},
 *   @OA\Property(property="data", ref="#/components/schemas/ArticleDetail")
 * )
 *
 * @OA\Schema(
 *   schema="SnippetSummary",
 *   type="object",
 *   required={"id","title","slug","description","language","status","views","likes","author","tags","created_at"},
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="slug", type="string"),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="language", type="string"),
 *   @OA\Property(property="status", type="string", enum={"draft","published","archived"}),
 *   @OA\Property(property="views", type="integer"),
 *   @OA\Property(property="likes", type="integer"),
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="author", ref="#/components/schemas/AuthorSummary"),
 *   @OA\Property(property="tags", type="array", @OA\Items(ref="#/components/schemas/TagResource")),
 *   @OA\Property(property="created_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *   schema="SnippetDetail",
 *   allOf={
 *     @OA\Schema(ref="#/components/schemas/SnippetSummary"),
 *     @OA\Schema(
 *       @OA\Property(property="code", type="string")
 *     )
 *   }
 * )
 *
 * @OA\Schema(
 *   schema="SnippetCollectionResponse",
 *   type="object",
 *   required={"data","meta"},
 *   @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/SnippetSummary")),
 *   @OA\Property(property="meta", ref="#/components/schemas/PaginationMeta")
 * )
 *
 * @OA\Schema(
 *   schema="SnippetSingleResponse",
 *   type="object",
 *   required={"data"},
 *   @OA\Property(property="data", ref="#/components/schemas/SnippetDetail")
 * )
 *
 * @OA\Schema(
 *   schema="ArticleUpsertRequest",
 *   type="object",
 *   required={"title","excerpt","content"},
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="excerpt", type="string"),
 *   @OA\Property(property="content", type="string"),
 *   @OA\Property(property="status", type="string", enum={"draft","published","archived"}),
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="tags", type="array", @OA\Items(type="integer"))
 * )
 *
 * @OA\Schema(
 *   schema="SnippetUpsertRequest",
 *   type="object",
 *   required={"title","code","language"},
 *   @OA\Property(property="title", type="string"),
 *   @OA\Property(property="description", type="string", nullable=true),
 *   @OA\Property(property="code", type="string"),
 *   @OA\Property(property="language", type="string"),
 *   @OA\Property(property="status", type="string", enum={"draft","published","archived"}),
 *   @OA\Property(property="published_at", type="string", format="date-time", nullable=true),
 *   @OA\Property(property="tags", type="array", @OA\Items(type="integer"))
 * )
 *
 * @OA\Schema(
 *   schema="TagUpsertRequest",
 *   type="object",
 *   required={"name"},
 *   @OA\Property(property="name", type="string")
 * )
 *
 * @OA\Schema(
 *   schema="AuthRegisterRequest",
 *   type="object",
 *   required={"name","email","password","password_confirmation"},
 *   @OA\Property(property="name", type="string"),
 *   @OA\Property(property="email", type="string", format="email"),
 *   @OA\Property(property="password", type="string", minLength=8),
 *   @OA\Property(property="password_confirmation", type="string", minLength=8)
 * )
 *
 * @OA\Schema(
 *   schema="AuthLoginRequest",
 *   type="object",
 *   required={"email","password"},
 *   @OA\Property(property="email", type="string", format="email"),
 *   @OA\Property(property="password", type="string", minLength=8)
 * )
 *
 * @OA\Schema(
 *   schema="AuthData",
 *   type="object",
 *   required={"user","token"},
 *   @OA\Property(property="user", ref="#/components/schemas/UserSummary"),
 *   @OA\Property(property="token", type="string")
 * )
 *
 * @OA\Schema(
 *   schema="AuthResponse",
 *   type="object",
 *   required={"message","data"},
 *   @OA\Property(property="message", type="string"),
 *   @OA\Property(property="data", ref="#/components/schemas/AuthData")
 * )
 *
 * @OA\Schema(
 *   schema="MeResponse",
 *   type="object",
 *   required={"data"},
 *   @OA\Property(
 *     property="data",
 *     type="object",
 *     required={"user"},
 *     @OA\Property(property="user", ref="#/components/schemas/UserSummary")
 *   )
 * )
 *
 * @OA\Schema(
 *   schema="MessageResponse",
 *   type="object",
 *   required={"message"},
 *   @OA\Property(property="message", type="string")
 * )
 *
 * @OA\Schema(
 *   schema="ValidationErrorResponse",
 *   type="object",
 *   required={"message","errors"},
 *   @OA\Property(property="message", type="string", example="Validation failed"),
 *   @OA\Property(property="errors", type="object")
 * )
 */
class Schemas
{
}
