<?php

use App\Enum\StatusEnum;
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
        Schema::create('payments', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignUuid('loan_id')->references('id')->on('loans')->cascadeOnDelete();
            $table->enum('status',[StatusEnum::PENDING->value, StatusEnum::PAID->value]);
            $table->decimal('amount', 10, 2, true);
            $table->date('schedule_payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
