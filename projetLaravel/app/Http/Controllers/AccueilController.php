<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Service;

class AccueilController extends Controller
{
    public function index()
    {
        return view('accueil', [
            'nbServices' => Service::count(),
            'nbIndisponibles' => Service::where('etat', 'indisponible')->count(),
            'nbEntrees' => Journal::count(),
        ]);
    }
}
