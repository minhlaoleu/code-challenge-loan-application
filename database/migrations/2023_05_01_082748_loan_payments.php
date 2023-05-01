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
        Schema::create('loan_payments', static function (Blueprint $table) {
            $table->foreignUuid('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignUuid('loan_id')->references('id')->on('loans')->cascadeOnDelete();
            $table->decimal('amount', 10, 2, true);
            $table->foreignUuid('status_id')->references('id')->on('status')->cascadeOnDelete();
            $table->unique(['user_id','loan_id','status_id'],'idx_user_id_loan_id_status_id');
            $table->dateTime('payment_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
