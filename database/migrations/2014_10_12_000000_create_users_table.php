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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('username')->unique();
            $table->string('phone')->unique();
            // $table->string('address')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('password');
            $table->integer('role')->default(0);
            // $table->string('admin_user_category_id')->nullable();
            $table->foreignId('admin_user_category_id')->nullable()->constrained()->nullOnDelete();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
