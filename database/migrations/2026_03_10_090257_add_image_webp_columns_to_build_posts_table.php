<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE build_posts ADD COLUMN image_preview_1_webp MEDIUMBLOB NULL AFTER image_preview_1_blob');
        DB::statement('ALTER TABLE build_posts ADD COLUMN image_preview_2_webp MEDIUMBLOB NULL AFTER image_preview_2_blob');
        DB::statement('ALTER TABLE build_posts ADD COLUMN image_preview_3_webp MEDIUMBLOB NULL AFTER image_preview_3_blob');
        DB::statement('ALTER TABLE build_posts ADD COLUMN image_preview_4_webp MEDIUMBLOB NULL AFTER image_preview_4_blob');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_posts', function ($table) {
            $table->dropColumn([
                'image_preview_1_webp',
                'image_preview_2_webp',
                'image_preview_3_webp',
                'image_preview_4_webp',
            ]);
        });
    }
};
