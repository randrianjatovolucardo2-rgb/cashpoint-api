<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashPoint;
use App\Models\Cloture;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $donnees = $request->validate([
            'telephone' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('telephone', $donnees['telephone'])->first();

        if (!$user || !Hash::check($donnees['password'], $user->password)) {
            return response()->json([
                'message' => 'Téléphone ou mot de passe incorrect.',
            ], 401);
        }

        $token = $user->createToken('application-cashpoint')->plainTextToken;

        return response()->json([
            'token' => $token,
            'utilisateur' => [
                'id' => $user->id,
                'nom' => $user->name,
                'telephone' => $user->telephone,
                'role' => $user->role,
                'agent_id' => $user->agent_id,
            ],
        ]);
    }

    public function espaceAgent(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'agent' || !$user->agent_id) {
            return response()->json([
                'message' => 'Accès réservé aux agents.',
            ], 403);
        }

        $cashPoint = CashPoint::where('agent_id', $user->agent_id)->first();

        if (!$cashPoint) {
            return response()->json([
                'message' => 'Aucun Cash Point associé.',
            ], 404);
        }

        $transactions = Transaction::where('agent_id', $user->agent_id)
            ->orderByDesc('date_transaction')
            ->get();

        return response()->json([
            'agent' => [
                'id' => $user->agent_id,
                'nom' => $user->name,
            ],
            'cash_point' => $cashPoint,
            'transactions' => $transactions,
        ]);
    }

    public function transactionAgent(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'agent' || !$user->agent_id) {
            return response()->json([
                'message' => 'Accès réservé aux agents.',
            ], 403);
        }

        $donnees = $request->validate([
            'operateur' => 'required|in:MVola,Orange Money,Airtel Money',
            'type_operation' => 'required|in:Dépôt,Retrait',
            'montant' => 'required|integer|min:1',
            'commission' => 'nullable|integer|min:0',
            'reference' => 'nullable|string',
        ]);

        $transaction = DB::transaction(function () use ($user, $donnees) {
            $cashPoint = CashPoint::where('agent_id', $user->agent_id)
                ->lockForUpdate()
                ->firstOrFail();

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
                'agent_id' => $user->agent_id,
                'cash_point_id' => $cashPoint->id,
                'operateur' => $donnees['operateur'],
                'type_operation' => $donnees['type_operation'],
                'montant' => $montant,
                'commission' => $donnees['commission'] ?? 0,
                'reference' => $donnees['reference'] ?? null,
                'date_transaction' => now(),
            ]);
        });

        return response()->json($transaction, 201);
    }

    public function clotureAgent(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'agent' || !$user->agent_id) {
            return response()->json([
                'message' => 'Accès réservé aux agents.',
            ], 403);
        }

        $cashPoint = CashPoint::where('agent_id', $user->agent_id)
            ->firstOrFail();

        $donnees = $request->validate([
            'solde_theorique_especes' => 'required|integer|min:0',
            'solde_reel_especes' => 'required|integer|min:0',
            'solde_theorique_mvola' => 'required|integer|min:0',
            'solde_reel_mvola' => 'required|integer|min:0',
            'solde_theorique_orange' => 'required|integer|min:0',
            'solde_reel_orange' => 'required|integer|min:0',
            'solde_theorique_airtel' => 'required|integer|min:0',
            'solde_reel_airtel' => 'required|integer|min:0',
        ]);

        $cloture = Cloture::create([
            'agent_id' => $user->agent_id,
            'cash_point_id' => $cashPoint->id,

            'solde_theorique_especes' =>
                $donnees['solde_theorique_especes'],

            'solde_reel_especes' =>
                $donnees['solde_reel_especes'],

            'solde_theorique_mvola' =>
                $donnees['solde_theorique_mvola'],

            'solde_reel_mvola' =>
                $donnees['solde_reel_mvola'],

            'solde_theorique_orange' =>
                $donnees['solde_theorique_orange'],

            'solde_reel_orange' =>
                $donnees['solde_reel_orange'],

            'solde_theorique_airtel' =>
                $donnees['solde_theorique_airtel'],

            'solde_reel_airtel' =>
                $donnees['solde_reel_airtel'],

            'date_cloture' => now(),
        ]);

        return response()->json($cloture, 201);
    }

    public function logout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ?->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }
}