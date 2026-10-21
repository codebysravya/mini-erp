<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number', 30)->unique();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('DRAFT');
            // Denormalised total, written once in the same transaction as the items.
            // Makes the dashboard / supplier-spend aggregates a single indexed SUM.
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->timestamp('ordered_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();

            // Supplier-spend report: WHERE status = 'RECEIVED' GROUP BY supplier_id (covering for SUM).
            $table->index(['status', 'supplier_id', 'total_amount']);
            // Dashboard "latest 5" and status filtered lists.
            $table->index(['status', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};