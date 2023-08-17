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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->double('price');
            $table->string('sifat');
            $table->double('eni');
            $table->double('gramm');
            $table->double('boyi');
            $table->string('color');
            $table->foreignId('ishlab_chiqarish_turi_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('mahsulot_tola_id')->nullable()->constrained()->nullOnDelete();
            $table->string('brand');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
