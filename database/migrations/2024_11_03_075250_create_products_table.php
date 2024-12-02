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
            $table->text('description')->nullable();
            $table->enum('type', ['user', 'system'])->index()->default('system');
            $table->enum('status', ['published', 'draft'])->index()->default('draft');
            $table->decimal('price', 8, 2);
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->string('sharing_link')->nullable();           
            $table->foreignId('created_by')->nullable()->constrained('users')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->integer('order')->nullable();
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
