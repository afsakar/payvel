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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('unit_id');
            $table->unsignedBigInteger('tax_id');
            $table->unsignedBigInteger('currency_id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('code');
            $table->decimal('price', 15, 2);
            $table->string('category');
            $table->string('type');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete(null);
            $table->foreign('tax_id')->references('id')->on('taxes')->onDelete(null);
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
