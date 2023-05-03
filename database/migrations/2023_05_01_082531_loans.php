<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enum\StatusEnum;

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
            $table->decimal('balanced', 10, 2, true)->default(0);
            $table->foreignUuid('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->enum('status',[StatusEnum::PENDING->value, StatusEnum::APPROVED->value, StatusEnum::PAID->value]);
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
