<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cloture extends Model
{
    protected $fillable = [
        'agent_id',
        'cash_point_id',
        'solde_theorique_especes',
        'solde_reel_especes',
        'solde_theorique_mvola',
        'solde_reel_mvola',
        'solde_theorique_orange',
        'solde_reel_orange',
        'solde_theorique_airtel',
        'solde_reel_airtel',
        'date_cloture',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function cashPoint()
    {
        return $this->belongsTo(CashPoint::class);
    }
}