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
        Schema::create('user_product_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained('products')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreignId('user_id')->nullable()->constrained('users')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->enum('platform', ['web', 'mobile'])->nullable()->default('web');
            $table->longText('user_agent')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_product_logs');
    }
};
