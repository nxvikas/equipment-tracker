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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_number')->unique();
            $table->string('name');
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->unique();
            $table->enum('status', \App\Http\Enums\StatusEquipment::values());
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price')->nullable();
            $table->date('warranty_date')->nullable();
            $table->string('qr_code')->nullable()->unique();
            $table->string('notes')->nullable();
            $table->string('status_comment')->nullable();
            $table->foreignIdFor(\App\Models\User::class,'current_user_id')->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Location::class)->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Category::class)->nullable()->constrained();


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
