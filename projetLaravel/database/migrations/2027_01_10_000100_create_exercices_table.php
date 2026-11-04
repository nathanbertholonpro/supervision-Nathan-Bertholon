<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercices', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->dateTime('debut');
            $table->dateTime('fin');
            $table->dateTime('demarre_a')->nullable();       // heure réelle du lancement
            $table->unsignedInteger('acceleration')->default(20);
            $table->dateTime('fige_a')->nullable();          // mode recette : l'horloge est arrêtée à cette heure
            $table->dateTime('detecte_a')->nullable();       // l'incident est détecté (délais contractuels des clients)
            $table->dateTime('important_depuis')->nullable(); // l'incident est qualifié d'important (délais NIS 2)
            $table->dateTime('malveillance_a')->nullable();  // une action malveillante est suspectée
            $table->dateTime('reputation_a')->nullable();    // l'incident est relayé par les médias
            $table->unsignedInteger('pertes_estimees')->nullable(); // pertes directes estimées, en euros
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercices');
    }
};
