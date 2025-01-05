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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string("invoice_number")->unique();
            $table->foreignId("customer_id")->constrained()->onDelete("cascade");
            $table->foreignId("slip_id")->constrained()->onDelete("cascade");
            $table->decimal("total_amount", 15, 2)->default(0.00);
            $table->decimal("transport_cost", 15, 2)->default(0);
            $table->decimal("total_dpp", 15, 2)->default(0);
            $table->decimal("ppn", 15, 2)->default(0);
            $table->decimal("pph_23", 15, 2)->default(0);
            $table->date("due_date");
            $table->enum('status', ['open', 'paid', 'unpaid'])->default('open');

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
