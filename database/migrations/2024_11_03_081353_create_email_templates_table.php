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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->enum('status', ['published', 'draft'])->index()->default('draft');
            $table->enum('email_type', ['schedule', 'direct'])->default('direct');
            $table->string('email_subject', 255)->nullable();
            $table->longText('email_header')->nullable();
            $table->longText('email_body')->nullable();
            $table->longText('email_footer')->nullable();
            $table->integer('reminder_period');
            $table->longText('schedule_periods')->nullable();
            $table->longText('sensitive_times')->nullable();
            $table->string('sms_message')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_active_web')->default(true);
            $table->boolean('is_active_mobile')->default(true);
            $table->boolean('is_active_sms')->default(false);
            $table->boolean('is_tracking_web')->default(true);
            $table->boolean('is_tracking_mobile')->default(true);
            $table->boolean('is_notification')->default(false);
            $table->integer('transactional_message_id')->nullable();
            $table->string('notification_title', 255)->nullable();
            $table->text('notification_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
