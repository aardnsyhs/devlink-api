<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TagRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $tag = $this->route('tag');
    $tagId = is_object($tag) ? $tag->id : $tag;

    return [
      'name' => [
        'required',
        'string',
        'max:255',
        Rule::unique('tags', 'name')->ignore($tagId),
      ],
    ];
  }
}
