<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equipment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Equipment::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('action_type', \App\Http\Enums\TypeEquipmentHistory::values());
            $table->foreignIdFor(\App\Models\User::class, 'user_id')->constrained();
            $table->foreignIdFor(\App\Models\User::class, 'from_user_id')->nullable()->constrained();
            $table->foreignIdFor(\App\Models\User::class, 'to_user_id')->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Location::class, 'from_location_id')->nullable()->constrained();
            $table->foreignIdFor(\App\Models\Location::class, 'to_location_id')->nullable()->constrained();
            $table->string('old_status')->nullable();
            $table->string('new_status')->nullable();
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_histories');
    }
};
