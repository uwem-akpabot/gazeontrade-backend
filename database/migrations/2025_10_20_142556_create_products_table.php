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
            $table->integer('category_id'); 
            $table->integer('subcategory_id'); 
            $table->string('product'); 
            $table->string('slug');
            $table->mediumText('description');
            $table->string('selling_price'); 
            $table->string('original_price')->nullable(); 
            $table->string('quantity')->nullable(); 
            $table->string('brand')->nullable(); 
            $table->string('delivery_time')->nullable(); 
            $table->string('image');
            $table->string('image2')->nullable();
            $table->string('image3')->nullable();
            $table->string('image4')->nullable();
            $table->string('image5')->nullable();
            $table->string('image6')->nullable();
            $table->string('image7')->nullable();
            $table->string('image8')->nullable();
            $table->tinyInteger('featured_product')->nullable(); 
            $table->tinyInteger('popular_product')->nullable(); 
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