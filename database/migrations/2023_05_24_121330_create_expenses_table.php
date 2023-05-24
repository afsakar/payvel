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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('corporation_id')->default(0);
            $table->unsignedBigInteger('category_id');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->string('type');
            $table->timestamp('due_at')->nullable();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete(null);
            $table->foreign('category_id')->references('id')->on('categories')->onDelete(null);
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
        Schema::dropIfExists('expenses');
    }
};
