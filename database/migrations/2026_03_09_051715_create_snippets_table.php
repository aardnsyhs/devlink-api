<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('snippets', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained()->cascadeOnDelete();
      $table->string('title');
      $table->string('slug')->unique();
      $table->text('description')->nullable();
      $table->longText('code');
      $table->string('language'); // php, js, python, etc.
      $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
      $table->unsignedInteger('views')->default(0);
      $table->unsignedInteger('likes')->default(0);
      $table->timestamp('published_at')->nullable();
      $table->timestamps();
      $table->softDeletes();

      $table->index(['status', 'published_at']);
      $table->index('language');
      $table->index('user_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('snippets');
  }
};
