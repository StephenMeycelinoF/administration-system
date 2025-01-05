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
        Schema::create('slips', function (Blueprint $table) {
            $table->id();
            $table->string('slip_code')->unique();
            $table->string('plat_number');
            $table->string('driver_name');
            $table->string('delivery_order');
            $table->decimal('bruto_muat', 15, 2);
            $table->decimal('tara_muat', 15, 2);
            $table->decimal('bruto_bongkar', 15, 2);
            $table->decimal('tara_bongkar', 15, 2);
            $table->date('date_slip');
            
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slips');
    }
};
