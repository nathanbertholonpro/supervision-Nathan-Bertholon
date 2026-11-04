<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('destinataire');
            $table->string('objet');
            $table->string('regle', 20);                     // nis2, nis2_final ou contrat
            $table->unsignedInteger('delai_minutes')->nullable();
            $table->foreignId('depend_de_id')->nullable()->constrained('notifications')->nullOnDelete();
            $table->string('client', 10)->nullable();
            $table->dateTime('envoyee_a')->nullable();
            $table->string('envoyee_par')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
