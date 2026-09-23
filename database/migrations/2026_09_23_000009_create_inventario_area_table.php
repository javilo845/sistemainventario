<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained('areas')->cascadeOnDelete();
            $table->decimal('cantidad', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['item_id', 'area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_area');
    }
};
