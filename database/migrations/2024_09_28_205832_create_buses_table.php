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
        // Tạo bảng bus_statuses trước
        Schema::create('bus_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Ví dụ: Đang hoạt động, Đang sửa chữa, Tạm ngừng
            $table->timestamps();
        });

        // Sau đó tạo bảng buses (có foreign key tới bus_statuses)
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_id');
            $table->unsignedBigInteger('garage_id');
            $table->unsignedBigInteger('status_id'); // FK đến bus_statuses
            $table->string('bus_number')->unique(); // Biển số xe

            $table->foreign('model_id')->references('id')->on('bus_models')->onDelete('cascade');
            $table->foreign('garage_id')->references('id')->on('garages')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('bus_statuses')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop bảng buses trước vì nó phụ thuộc vào bus_statuses
        Schema::dropIfExists('buses');
        Schema::dropIfExists('bus_statuses');
    }
};
