<?php

namespace Database\Seeders;

use App\Models\Snippet;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class SnippetSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $user = User::first() ?? User::factory()->create([
      'name' => 'Ardiansyah',
      'email' => 'ardiansyah@devlink.com',
    ]);

    $tagIds = Tag::pluck('id')->toArray();

    Snippet::factory(20)->published()->create(['user_id' => $user->id])
      ->each(fn($snippet) => $snippet->tags()->attach(
        fake()->randomElements($tagIds, rand(1, 3))
      ));

    Snippet::factory(5)->create(['user_id' => $user->id]); // drafts
  }
}
