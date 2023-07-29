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
            // $table->string('title');
            $table->double('price');
            $table->string('sifat');
            $table->double('eni');
            $table->double('gramm');
            $table->double('boyi');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');            
            $table->string('color');
            // $table->string('ishlab_chiqarish_turi');
            $table->foreignId('ishlab_chiqarish_turi_id')->nullable()->constrained()->nullOnDelete();
            // $table->string('mahsulot_turi');
            $table->foreignId('mahsulot_tola_id')->nullable()->constrained()->nullOnDelete();
            $table->string('brand');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Schema::create('photos', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('product_id')->constrained()->onDelete('cascade');
        //     $table->string('url');
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
