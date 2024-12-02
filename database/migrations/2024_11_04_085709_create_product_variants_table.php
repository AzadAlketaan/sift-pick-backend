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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->string('variant_name'); // E.g., "Small", "Large", "Red", "Blue"
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('stock')->default(0); // Stock for this variant
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
