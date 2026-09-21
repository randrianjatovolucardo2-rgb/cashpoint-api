<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'agent_id',
        'cash_point_id',
        'operateur',
        'type_operation',
        'montant',
        'commission',
        'reference',
        'date_transaction',
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