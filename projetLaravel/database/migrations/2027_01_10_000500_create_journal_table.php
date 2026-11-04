<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journal', function (Blueprint $table) {
            $table->id();
            $table->dateTime('heure');
            $table->string('categorie', 20);                 // fait, decision, action, notification
            $table->text('texte');
            $table->string('auteur');
            $table->string('source', 20)->default('centre'); // centre (saisie au poste), scenario ou sonde
            $table->foreignId('inject_id')->nullable()->constrained('injects')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal');
    }
};
