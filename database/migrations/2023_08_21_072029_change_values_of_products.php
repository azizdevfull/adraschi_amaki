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
        Schema::table('products', function (Blueprint $table) {
            $table->string('price')->change();
            $table->string('eni')->change();
            $table->string('boyi')->change();

            // Modify the discount column
            $table->string('discount')->nullable()->change(); // Change to nullable to allow truncation

            $table->string('size')->change();
            $table->string('gramm')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('eni');
            $table->dropColumn('boyi');
            $table->dropColumn('discount');
            $table->dropColumn('size');
            $table->dropColumn('gramm');
        });
    }
};
