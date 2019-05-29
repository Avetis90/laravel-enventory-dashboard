<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Converter extends Model
{
    protected $fillable = [
        'converter_type',
        'prefix',
        'service_options',
        'battery_packing',
        'battery_type',
        'description',
        'description_cn'
    ];
}