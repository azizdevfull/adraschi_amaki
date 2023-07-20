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
            $table->string('title');
            $table->double('price');
            $table->longText('body');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');            
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('color')->nullable();
            $table->text('compatibility')->nullable();
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
