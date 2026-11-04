<?php

namespace App\Http\Controllers;

use App\Models\Service;

/**
 * Moniteur 1 — les services et leur disponibilité.
 */
class ServicesController extends Controller
{
    public function index()
    {
        return view('moniteurs.services', [
            'services' => Service::all(),
        ]);
    }
}
