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
        Schema::create('checks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('corporation_id');
            $table->string('number')->unique();
            $table->decimal('amount', 15, 2);
            $table->text('description');
            $table->timestamp('issue_date');
            $table->timestamp('due_date');
            $table->timestamp('paid_date')->nullable();
            $table->string('type');
            $table->string('status');
            $table->string('image')->nullable();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete(null);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete(null);
            $table->foreign('corporation_id')->references('id')->on('corporations')->onDelete(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checks');
    }
};
