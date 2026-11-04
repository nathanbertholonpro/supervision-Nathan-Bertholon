<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Une entrée du journal d'incident : ce qui s'est passé ou ce qui a été décidé, et quand.
 */
class Journal extends Model
{
    use HasFactory;

    public const CATEGORIES = ['fait', 'decision', 'action', 'notification'];

    protected $table = 'journal';

    protected $fillable = ['heure', 'categorie', 'texte', 'auteur', 'source', 'inject_id'];

    protected function casts(): array
    {
        return ['heure' => 'immutable_datetime'];
    }
}
