<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();

            // Polymorphic relationship
            $table->morphs('seoable');

            // Core meta
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('keywords')->nullable();

            // Focus keyword for analysis
            $table->string('focus_keyword')->nullable();

            // Technical SEO
            $table->string('canonical_url')->nullable();
            $table->string('robots')->default('index, follow');

            // Open Graph
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            // Twitter Card
            $table->string('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image')->nullable();

            // Schema.org
            $table->string('schema_type')->nullable();

            // Score
            $table->unsignedTinyInteger('seo_score')->default(0);

            $table->timestamps();

            // Ensure one SEO meta per model
            $table->unique(['seoable_type', 'seoable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_meta');
    }
};
