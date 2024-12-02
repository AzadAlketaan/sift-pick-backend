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
        Schema::create('user_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')
                ->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->foreignId('email_template_id')->nullable()->constrained('email_templates')
                ->onDelete('SET NULL')->onUpdate('CASCADE');
            $table->enum('send_status', ['succeeded', 'canceled', 'pending', 'waiting', 'inProgress', 'faild'])->default('inProgress');
            $table->timestamp('schedule_send')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('error_msg')->nullable();
            $table->enum('platform', ['web', 'mobile'])->nullable()->default('web');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_emails');
    }
};
