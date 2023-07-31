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
        // Remove the foreign key constraint before dropping the column
        Schema::table('reklamas', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // Remove the column itself
        Schema::table('reklamas', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reklamas', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->after('id')->nullable();

            $table->foreign('category_id')->references('id')->on('categories')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }
};
