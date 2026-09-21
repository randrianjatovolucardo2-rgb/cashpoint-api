<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clotures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agent_id')
                ->constrained('agents')
                ->cascadeOnDelete();

            $table->foreignId('cash_point_id')
                ->constrained('cash_points')
                ->cascadeOnDelete();

            $table->integer('solde_theorique_especes');
            $table->integer('solde_reel_especes');

            $table->integer('solde_theorique_mvola');
            $table->integer('solde_reel_mvola');

            $table->integer('solde_theorique_orange');
            $table->integer('solde_reel_orange');

            $table->integer('solde_theorique_airtel');
            $table->integer('solde_reel_airtel');

            $table->timestamp('date_cloture')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clotures');
    }
};