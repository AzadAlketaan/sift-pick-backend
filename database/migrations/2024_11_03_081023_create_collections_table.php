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
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable(false);
            $table->enum('type', ['user', 'system'])->index()->default('system');
            $table->enum('status', ['published', 'draft'])->index()->default('draft');
            $table->enum('privacy', ['private', 'public'])->index()->default('private');
            $table->text('description')->nullable();
            $table->integer('order')->nullable();
            $table->enum('items_order', ['most_read', 'recently_added', 'recently_published'])->default('most_read');
            $table->foreignId('user_id')->nullable()->constrained('users')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->boolean('is_suggested')->default(0);
            $table->string('sharing_link')->nullable();
            $table->timestamp('set_suggested_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
