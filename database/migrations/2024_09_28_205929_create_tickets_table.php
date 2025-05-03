<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('schedule_id');
            $table->string('full_name');
            $table->string('phone_number');
            $table->string('email');
            $table->string('pickup_point');
            $table->string('dropoff_point');
            $table->decimal('price_paid', 10, 2); // New field to store the total price based on ticket quantity
            $table->date('date')->nullable(); // Ngày đặt vé (specific day for the booking)
            $table->timestamps();

            $table->foreign('schedule_id')->references('id')->on('route_schedules')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
