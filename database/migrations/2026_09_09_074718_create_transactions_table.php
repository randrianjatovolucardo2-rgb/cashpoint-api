<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('agents')
                ->cascadeOnDelete();

            $table->foreignId('cash_point_id')
                ->constrained('cash_points')
                ->cascadeOnDelete();

            $table->string('operateur');
            $table->string('type_operation');

            $table->integer('montant');
            $table->integer('commission')->default(0);

            $table->string('reference')->nullable();

            $table->timestamp('date_transaction')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};