<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
  /** @use HasFactory<\Database\Factories\ArticleFactory> */
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'user_id',
    'title',
    'slug',
    'excerpt',
    'content',
    'status',
    'views',
    'published_at',
  ];

  protected $casts = [
    'published_at' => 'datetime',
    'views' => 'integer',
  ];

  protected static function booted(): void
  {
    static::creating(function (Article $article) {
      $article->slug = Str::slug($article->title) . '-' . Str::random(6);
    });
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function tags()
  {
    return $this->belongsToMany(Tag::class);
  }

  public function scopePublished($query)
  {
    return $query->where('status', 'published')
      ->whereNotNull('published_at');
  }
}
