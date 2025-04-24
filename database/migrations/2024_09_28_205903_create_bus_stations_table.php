<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tạo bảng trạng thái trạm xe
        Schema::create('station_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ví dụ: Đang hoạt động, Tạm dừng, Đóng cửa
            $table->timestamps();
        });

        // Tạo bảng trạm xe
        Schema::create('bus_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tên trạm xe
            $table->unsignedBigInteger('garage_id'); // FK đến garages
            $table->unsignedBigInteger('province_id'); // FK đến provinces
            $table->unsignedBigInteger('status_id'); // FK đến station_statuses
            $table->string('address')->nullable(); // Địa chỉ
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('garage_id')->references('id')->on('garages')->onDelete('cascade');
            $table->foreign('province_id')->references('id')->on('provinces')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('station_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bus_stations');
        Schema::dropIfExists('station_statuses');
    }
};
