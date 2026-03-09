<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Snippet extends Model
{
  /** @use HasFactory<\Database\Factories\SnippetFactory> */
  use HasFactory, SoftDeletes;

  protected $fillable = [
    'user_id',
    'title',
    'slug',
    'description',
    'code',
    'language',
    'status',
    'views',
    'likes',
    'published_at',
  ];

  protected $casts = [
    'published_at' => 'datetime',
    'views' => 'integer',
    'likes' => 'integer',
  ];

  protected static function booted(): void
  {
    static::creating(function (Snippet $snippet) {
      if (blank($snippet->slug)) {
        $snippet->slug = Str::slug($snippet->title) . '-' . Str::random(6);
      }
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
