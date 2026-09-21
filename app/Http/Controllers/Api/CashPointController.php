<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashPoint;
use Illuminate\Http\Request;

class CashPointController extends Controller
{
    public function index()
    {
        return response()->json(
            CashPoint::with('agent')->get()
        );
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'nom' => 'required|string',
            'localisation' => 'nullable|string',
            'agent_id' => 'nullable|exists:agents,id',
            'solde_especes' => 'required|integer|min:0',
            'solde_mvola' => 'required|integer|min:0',
            'solde_orange' => 'required|integer|min:0',
            'solde_airtel' => 'required|integer|min:0',
        ]);

        $cashPoint = CashPoint::create($donnees);

        return response()->json($cashPoint, 201);
    }
}