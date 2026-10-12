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
    Schema::create('sales', function (Blueprint $table) {
        $table->id();
        $table->foreignId('processed_by')->constrained('users')->onDelete('cascade');
        $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
        $table->foreignId('membership_id')->nullable()->constrained()->onDelete('set null');
        
        $table->enum('transaction_type', ['court_rental', 'membership', 'walk_in']);
        $table->string('customer_name')->nullable();
        $table->decimal('amount_paid', 8, 2);
        $table->enum('payment_status', ['paid', 'refunded'])->default('paid');
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
