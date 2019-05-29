<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UbiOrders extends Model
{
    protected $fillable = [
    'order_id',
    'date',
    'tracking_number',
    'weight',
    'cost',
    'item_skus',
    'invoiced',
    ];
}
