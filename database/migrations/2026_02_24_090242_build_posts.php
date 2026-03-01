<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('build_posts', function (Blueprint $table) {
            $table->id();
            // Using text() or string() with a long length to accommodate long FB URLs
            $table->text('image_preview_1')->nullable();
            $table->text('image_preview_2')->nullable();
            $table->text('image_preview_3')->nullable();
            $table->text('image_preview_4')->nullable();
            $table->longText('text')->nullable();
            $table->integer('likes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('build_posts');
    }
};
