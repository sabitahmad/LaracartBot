<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->foreignId('product_id');
            $table->decimal('price', 10)->nullable();
            $table->foreignId('product_variation_image_id')->nullable()->references('id')->on('media');
            $table->integer('stock')->default(0); // Add stock column
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
