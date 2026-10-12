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
    Schema::create('memberships', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('plan_type', ['monthly', 'quarterly', 'annual']);
        $table->decimal('amount', 8, 2);
        $table->date('start_date');
        $table->date('end_date');
        $table->enum('status', ['pending', 'active', 'expired'])->default('pending');
        $table->string('receipt_image')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
