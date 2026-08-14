<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->text('blurb')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('effects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('description')->nullable();

            // The spring notation printed on the card, e.g. "spring(320, 24)".
            $table->string('param')->nullable();

            // Demo markup for the card's live preview. Kept as raw HTML because
            // each effect animates differently and there is no shared template.
            $table->longText('demo_html');

            $table->string('access_level')->default('free');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['category_id', 'position']);
            $table->index('access_level');
        });

        // Snippets and prompts carry their own access_level so a single effect
        // can ship a free CSS snippet alongside a Pro-only Claude prompt.
        Schema::create('effect_snippets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('effect_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->longText('content');
            $table->string('access_level')->default('free');
            $table->timestamps();

            $table->unique(['effect_id', 'type']);
        });

        Schema::create('effect_prompts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('effect_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->longText('content');
            $table->string('access_level')->default('free');
            $table->timestamps();

            $table->unique(['effect_id', 'type']);
        });

        Schema::create('effect_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('effect_id')->constrained()->cascadeOnDelete();
            $table->string('keyword');

            $table->unique(['effect_id', 'keyword']);
            $table->index('keyword');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('effect_keywords');
        Schema::dropIfExists('effect_prompts');
        Schema::dropIfExists('effect_snippets');
        Schema::dropIfExists('effects');
        Schema::dropIfExists('categories');
    }
};
