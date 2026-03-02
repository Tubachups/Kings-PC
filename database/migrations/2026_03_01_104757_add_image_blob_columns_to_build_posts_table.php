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
        // DB::statement('ALTER TABLE build_posts ADD COLUMN image_preview_1_blob MEDIUMBLOB NULL AFTER image_preview_1');
        // DB::statement('ALTER TABLE build_posts ADD COLUMN image_preview_2_blob MEDIUMBLOB NULL AFTER image_preview_2');
        // DB::statement('ALTER TABLE build_posts ADD COLUMN image_preview_3_blob MEDIUMBLOB NULL AFTER image_preview_3');
        // DB::statement('ALTER TABLE build_posts ADD COLUMN image_preview_4_blob MEDIUMBLOB NULL AFTER image_preview_4');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('build_posts', function ($table) {
            $table->dropColumn([
                'image_preview_1_blob',
                'image_preview_2_blob',
                'image_preview_3_blob',
                'image_preview_4_blob',
            ]);
        });
    }
};
