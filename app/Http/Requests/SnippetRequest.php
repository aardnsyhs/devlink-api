<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SnippetRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'title' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'code' => ['required', 'string'],
      'language' => ['required', 'string', 'max:50'],
      'status' => ['sometimes', 'in:draft,published,archived'],
      'published_at' => ['nullable', 'date'],
      'tags' => ['nullable', 'array'],
      'tags.*' => ['exists:tags,id'],
    ];
  }
}
