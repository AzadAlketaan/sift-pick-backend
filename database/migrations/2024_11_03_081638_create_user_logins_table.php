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
        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->enum('action', ['login', 'signup', 'logout'])->nullable();
            $table->enum('type', ['social', 'web', 'mobile'])->default('web');
            $table->enum('platform', ['dashboard', 'social', 'web', 'mobile'])->default('web');
            $table->longText('user_agent')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')
                ->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logins');
    }
};
