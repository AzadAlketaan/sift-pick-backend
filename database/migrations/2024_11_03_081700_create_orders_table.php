<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const STATUSES = [
        'processing',
        'paid',
        'completed',
        'failed',
        'on-hold',
        'refunded',
        'unpaid',
        'cancelled',
        'capture_pending',
        'captured',
        'expired',
        'refunded_accepted',
        'refunded_decline'
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', self::STATUSES)->default('processing')->index();
            $table->decimal('total_amount', 10, 2);

            $table->foreignId('user_id')->constrained()
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->foreignId('product_id')->nullable()->constrained('products')
                ->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->foreignId('country_id')->nullable()->constrained('countries')
                ->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
