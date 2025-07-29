<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        'correlative_number',
        'client_name',
        'client_rut',
        'description',
        'net_amount',
        'iva_amount',
        'total_amount',
    ];
}
