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
        Schema::create('users', static function (Blueprint $table) {
            $table->uuid('id')->primary()->autoIncrement();
            $table->string('first_name', 50);
            $table->string('last_name', 100);
            $table->string('email')->unique();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('password');
            $table->timestamp('last_logged_in')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
