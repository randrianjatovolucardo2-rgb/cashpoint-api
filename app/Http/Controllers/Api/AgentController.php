<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\CashPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    public function index()
    {
        return response()->json(Agent::all());
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'nom' => 'required|string',
            'telephone' => 'nullable|string',
            'localisation' => 'required|string',

            'nom_cash_point' => 'required|string',

            'solde_especes' => 'required|integer|min:0',
            'solde_mvola' => 'required|integer|min:0',
            'solde_orange' => 'required|integer|min:0',
            'solde_airtel' => 'required|integer|min:0',
        ]);

        $resultat = DB::transaction(function () use ($donnees) {

            $agent = Agent::create([
                'nom' => $donnees['nom'],
                'telephone' => $donnees['telephone'] ?? null,
                'localisation' => $donnees['localisation'],
            ]);

            $cashPoint = CashPoint::create([
                'nom' => $donnees['nom_cash_point'],
                'localisation' => $donnees['localisation'],
                'agent_id' => $agent->id,
                'solde_especes' => $donnees['solde_especes'],
                'solde_mvola' => $donnees['solde_mvola'],
                'solde_orange' => $donnees['solde_orange'],
                'solde_airtel' => $donnees['solde_airtel'],
            ]);

            return [
                'agent' => $agent,
                'cash_point' => $cashPoint,
            ];
        });

        return response()->json($resultat, 201);
    }
}