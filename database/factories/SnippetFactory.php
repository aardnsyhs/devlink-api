<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Snippet>
 */
class SnippetFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'user_id' => User::factory(),
      'title' => fake()->sentence(),
      'slug' => fake()->unique()->slug(),
      'description' => fake()->paragraph(),
      'code' => fake()->paragraphs(4, true),
      'language' => fake()->randomElement(['php', 'javascript', 'typescript', 'python', 'go']),
      'status' => 'draft',
      'views' => fake()->numberBetween(0, 2000),
      'likes' => fake()->numberBetween(0, 500),
      'published_at' => null,
    ];
  }

  public function published(): static
  {
    return $this->state([
      'status' => 'published',
      'published_at' => now()->subDays(rand(1, 30)),
    ]);
  }

  public function archived(): static
  {
    return $this->state(['status' => 'archived']);
  }
}
