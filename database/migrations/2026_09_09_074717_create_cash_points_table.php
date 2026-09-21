<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_points', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('localisation')->nullable();

            $table->foreignId('agent_id')
                ->nullable()
                ->constrained('agents')
                ->nullOnDelete();

            $table->integer('solde_especes')->default(0);
            $table->integer('solde_mvola')->default(0);
            $table->integer('solde_orange')->default(0);
            $table->integer('solde_airtel')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_points');
    }
};