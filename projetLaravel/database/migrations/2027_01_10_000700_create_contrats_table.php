<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->string('client', 10)->unique();
            $table->string('nom');
            $table->string('entite');
            $table->boolean('dora')->default(false);         // entité financière soumise à DORA
            $table->string('autorite')->nullable();
            $table->string('etats_membres')->default('FR');  // « FR;BE »
            $table->unsignedInteger('delai_notification_minutes');
            $table->string('localisation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
