<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'title' => ['required', 'string', 'max:255'],
      'excerpt' => ['required', 'string', 'max:500'],
      'content' => ['required', 'string'],
      'status' => ['sometimes', 'in:draft,published,archived'],
      'published_at' => ['nullable', 'date'],
      'tags' => ['nullable', 'array'],
      'tags.*' => ['exists:tags,id'],
    ];
  }
}
