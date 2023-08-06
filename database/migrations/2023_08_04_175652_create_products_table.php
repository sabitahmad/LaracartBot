<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('slug')->unique()->nullable(); //For future usages
            $table->text('description')->nullable();
            $table->boolean('is_variable')->default(false);
            $table->enum('status', ['draft', 'published', 'unpublished']);
            $table->integer('stock')->default(0)->nullable();
            $table->foreignId('product_category_id')->references('id')->on('product_categories');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('products');
    }
};
