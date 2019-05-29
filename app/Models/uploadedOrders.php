<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class uploadedOrders extends Model
{
    protected $fillable = [
    'order_id',
    'skus',
    'shipping_method',
    'country',
    'name',
    'state',
    'city',
    'address',
    'zip',
    'phone',
    'email',
    ];
}
