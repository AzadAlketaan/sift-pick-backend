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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('status', ['published', 'draft', 'expired'])->default('draft');            
            $table->integer("discount_value")->default(1); //checkit
            $table->longText('note')->nullable();
            $table->timestamp('published_at')->index()->nullable();
            $table->timestamp('expired_at')->index()->nullable();
                        
            $table->foreignId('product_id')->nullable()->constrained('products')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
