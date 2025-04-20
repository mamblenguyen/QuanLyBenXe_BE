<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bus_id'); // Khóa ngoại đến bảng buses
            $table->string('image_path'); // Đường dẫn ảnh
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('bus_id')->references('id')->on('buses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_images');
    }
};
