<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cloture;
use Illuminate\Http\Request;

class ClotureController extends Controller
{
    public function index()
    {
        return response()->json(
            Cloture::with(['agent', 'cashPoint'])
                ->orderByDesc('date_cloture')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'cash_point_id' => 'required|exists:cash_points,id',

            'solde_theorique_especes' => 'required|integer|min:0',
            'solde_reel_especes' => 'required|integer|min:0',

            'solde_theorique_mvola' => 'required|integer|min:0',
            'solde_reel_mvola' => 'required|integer|min:0',

            'solde_theorique_orange' => 'required|integer|min:0',
            'solde_reel_orange' => 'required|integer|min:0',

            'solde_theorique_airtel' => 'required|integer|min:0',
            'solde_reel_airtel' => 'required|integer|min:0',
        ]);

        $donnees['date_cloture'] = now();

        $cloture = Cloture::create($donnees);

        return response()->json($cloture, 201);
    }
}