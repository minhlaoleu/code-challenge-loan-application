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
        Schema::create('loans', static function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->smallInteger('term',false,true);
            $table->decimal('amount', 10, 2, true);
            $table->tinyInteger('status',false,true);
            $table->foreignUuid('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignUuid('status_id')->references('id')->on('status')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
