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
        Schema::create('parcels', function (Blueprint $table) {
            $table->id();
            $table->string('ic', 20)->nullable()->index();
            $table->string('recipient_name')->nullable();
            $table->string('sender_name')->nullable();
            $table->foreignId('courier_id')->constrained('couriers')->onDelete('cascade');
            $table->string('serial_number')->nullable()->unique();
            $table->string('tracking_number')->nullable()->unique();
            $table->string('parcel_size', 20);
            $table->decimal('amount', 8, 2)->default(0);
            $table->boolean('cod_id')->default(false);
            $table->decimal('cod_amount', 8, 2)->default(0);
            $table->text('notes')->nullable();
            $table->tinyInteger('status')->default(1)->index();
            $table->timestamps();

            // Additional indexes for performance
            $table->index('created_at');
            $table->index(['status', 'ic']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parcels');
    }
};
