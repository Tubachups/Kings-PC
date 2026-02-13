<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('pc_case')->nullable();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('pc_case')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('pc_case');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('pc_case');
        });
    }
};
