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
    { ád
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->date('birthday')->nullable();
            $table->enum('gender', ['Nam', 'Nữ', 'Khác'])->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('email', 255)->index();
            $table->string('password', 255);
            $table->string('avatar')->nullable();
            $table->text('address')->nullable();
            $table->string('identity_card', 12)->unique();
            $table->string('identity_issue_place', 255)->nullable();
            $table->date('identity_issue_date')->nullable();
            $table->enum('status', ['Đang hoạt động', 'Tạm dừng', 'Đã khóa'])->default('Đang hoạt động');
            $table->string('license', 10)->nullable();
            $table->date('license_issue_date')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->unsignedBigInteger('garage_id');
            $table->foreign('garage_id')->references('id')->on('garages')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Sử dụng UUID cho id
            $table->uuid('user_id')->nullable()->index(); // Thay đổi thành UUID
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
