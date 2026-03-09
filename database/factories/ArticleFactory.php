<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
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
      'excerpt' => fake()->paragraph(),
      'content' => fake()->paragraphs(5, true),
      'status' => 'draft',
      'views' => fake()->numberBetween(0, 1000),
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
