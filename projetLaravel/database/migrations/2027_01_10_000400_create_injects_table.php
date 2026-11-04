<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('injects', function (Blueprint $table) {
            $table->id();
            $table->dateTime('heure');
            $table->string('categorie', 20);
            $table->text('titre');
            $table->json('donnees')->nullable();
            $table->dateTime('joue_a')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('injects');
    }
};
