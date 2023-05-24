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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('corporation_id');
            $table->unsignedBigInteger('waybill_id')->nullable();
            $table->unsignedBigInteger('with_holding_id');
            $table->string('number')->unique();
            $table->string('status');
            $table->timestamp('issue_date')->nullable();
            $table->decimal('discount', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete(null);
            $table->foreign('corporation_id')->references('id')->on('corporations')->onDelete(null);
            $table->foreign('waybill_id')->references('id')->on('waybills')->onDelete(null);
            $table->foreign('with_holding_id')->references('id')->on('with_holdings')->onDelete(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
