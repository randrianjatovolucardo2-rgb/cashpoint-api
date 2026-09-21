<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashPoint;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index()
    {
        return response()->json(
            Transaction::with(['agent', 'cashPoint'])
                ->orderByDesc('date_transaction')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $donnees = $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'cash_point_id' => 'required|exists:cash_points,id',
            'operateur' => 'required|in:MVola,Orange Money,Airtel Money',
            'type_operation' => 'required|in:Dépôt,Retrait',
            'montant' => 'required|integer|min:1',
            'commission' => 'nullable|integer|min:0',
            'reference' => 'nullable|string',
        ]);

        $transaction = DB::transaction(function () use ($donnees) {
            $cashPoint = CashPoint::lockForUpdate()
                ->findOrFail($donnees['cash_point_id']);

            if ($cashPoint->agent_id != $donnees['agent_id']) {
                throw ValidationException::withMessages([
                    'agent_id' => 'Cet agent ne correspond pas à ce Cash Point.',
                ]);
            }

            $montant = $donnees['montant'];

            $champOperateur = match ($donnees['operateur']) {
                'MVola' => 'solde_mvola',
                'Orange Money' => 'solde_orange',
                'Airtel Money' => 'solde_airtel',
            };

            if ($donnees['type_operation'] === 'Dépôt') {
                if ($cashPoint->$champOperateur < $montant) {
                    throw ValidationException::withMessages([
                        'montant' => 'Solde électronique insuffisant.',
                    ]);
                }

                $cashPoint->solde_especes += $montant;
                $cashPoint->$champOperateur -= $montant;
            }

            if ($donnees['type_operation'] === 'Retrait') {
                if ($cashPoint->solde_especes < $montant) {
                    throw ValidationException::withMessages([
                        'montant' => 'Solde espèces insuffisant.',
                    ]);
                }

                $cashPoint->solde_especes -= $montant;
                $cashPoint->$champOperateur += $montant;
            }

            $cashPoint->save();

            return Transaction::create([
                ...$donnees,
                'commission' => $donnees['commission'] ?? 0,
                'date_transaction' => now(),
            ]);
        });

        return response()->json($transaction, 201);
    }
}