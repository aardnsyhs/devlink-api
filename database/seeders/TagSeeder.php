<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $tags = [
      'PHP',
      'Laravel',
      'JavaScript',
      'TypeScript',
      'React',
      'Next.js',
      'Docker',
      'Redis',
      'MySQL',
      'API'
    ];

    foreach ($tags as $tag) {
      Tag::create([
        'name' => $tag,
        'slug' => Str::slug($tag),
      ]);
    }
  }
}
