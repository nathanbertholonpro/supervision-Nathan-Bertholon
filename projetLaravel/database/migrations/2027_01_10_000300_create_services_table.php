<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('client', 10);                    // code du client (CFU, PPAY…)
            $table->string('nom');
            $table->string('type', 20);                      // infogerance ou colocation
            $table->unsignedTinyInteger('criticite');        // 1 : fonction critique ou importante du client
            $table->unsignedInteger('gtr_minutes');          // garantie de temps de rétablissement (contrat)
            $table->unsignedInteger('utilisateurs')->default(0);
            $table->string('composants')->nullable();        // « VIRT-1;SAN-A » (ainsi l'a laissé le prestataire)
            $table->string('etat', 20)->default('normal');
            $table->dateTime('etat_depuis')->nullable();
            $table->dateTime('indisponible_depuis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
