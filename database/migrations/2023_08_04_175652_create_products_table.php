<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 10, 2);
            $table->string('slug')->unique()->nullable(); //For future usages
            $table->text('description')->nullable();
            $table->boolean('is_variable')->default(false);
            $table->string('product_picture_ids');
            $table->enum('status', ['draft','published', 'unpublished']);
            $table->foreignId('product_category_id')->constrained('product_categories');
            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
