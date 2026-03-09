<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = User::factory()->create([
      'name' => 'Ardiansyah',
      'email' => 'ardiansyah@devlink.com',
    ]);

    $tagIds = Tag::pluck('id')->toArray();

    Article::factory(20)->published()->create(['user_id' => $user->id])
      ->each(fn($article) => $article->tags()->attach(
        fake()->randomElements($tagIds, rand(1, 3))
      ));

    Article::factory(5)->create(['user_id' => $user->id]); // drafts
  }
}
