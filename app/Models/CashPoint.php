<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashPoint extends Model
{
    protected $fillable = [
        'nom',
        'localisation',
        'agent_id',
        'solde_especes',
        'solde_mvola',
        'solde_orange',
        'solde_airtel',
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}