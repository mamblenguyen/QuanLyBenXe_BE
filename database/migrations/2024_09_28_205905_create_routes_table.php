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
        // Create route_statuses table
        Schema::create('route_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('schedule_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        // Create routes table
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('departure_station_id');
            $table->unsignedBigInteger('arrival_station_id');
            $table->unsignedBigInteger('garage_id');
            $table->unsignedBigInteger('bus_id');
            $table->uuid('driver_id');
            $table->unsignedBigInteger('route_status_id')->default(1); // Add route status foreign key

            $table->timestamps();

            // Foreign keys
            $table->foreign('garage_id')->references('id')->on('garages')->onDelete('cascade');
            $table->foreign('departure_station_id')->references('id')->on('bus_stations')->onDelete('cascade');
            $table->foreign('arrival_station_id')->references('id')->on('bus_stations')->onDelete('cascade');
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('route_status_id')->references('id')->on('route_statuses')->onDelete('cascade');
        });

        // Create route_schedules table
        Schema::create('route_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id');
            $table->time('departure_time');
            $table->time('arrival_time');
            $table->decimal('ticket_price', 10, 2);
            $table->timestamps();
            $table->unsignedBigInteger('schedule_status_id')->default(1); 

            $table->foreign('schedule_status_id')->references('id')->on('schedule_statuses')->onDelete('cascade');

            // Foreign key to routes table
            $table->foreign('route_id')->references('id')->on('routes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('route_schedules');
        Schema::dropIfExists('routes');
        Schema::dropIfExists('route_statuses');
    }
};
