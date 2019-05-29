<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\ShipcostHelper;

class InvoiceOrders extends Model
{
    protected $fillable = [
    'order_id',
    'prefix',
    'track_num',
    'sku',
    'weight',
    'product_cost',
    'shipping_cost',
    'status',
    ];
	
    protected $appends = ['vendor_sku', 'country', 'shipcost_rmb', 'shipcost_usd', 'total_cost'];
	
    public function getVendorSkuAttribute()
    {
        return ShipcostHelper::getCustomerSku($this->getAttribute('sku'));
    }
	
    public function getTotalCostAttribute()
    {   
        $total_cost = $this->getAttribute('product_cost') + $this->getAttribute('shipcost_usd');
        return $total_cost;
    }
    
    public function getCountryAttribute()
    {   
        $country = (uploadedOrders::where('order_id', $this->getAttribute('order_id'))->first()) ? uploadedOrders::where('order_id', $this->getAttribute('order_id'))->first()->country : "";
        return $country;
    }
    
    public function getShipcostUsdAttribute()
    {
        $ship_cost = ShipcostHelper::getShipcost($this->getAttribute('shipping_cost'), $this->getAttribute('weight'), $this->getAttribute('track_num'), $this->getAttribute('order_id'));
        return number_format(($ship_cost / 6.4), 2, '.', '');
    }
    
    public function getShipcostRmbAttribute($value)
    {
        $ship_cost = ShipcostHelper::getShipcost($this->getAttribute('shipping_cost'), $this->getAttribute('weight'), $this->getAttribute('track_num'), $this->getAttribute('order_id'));
        return number_format(($ship_cost), 2, '.', '');
    }
}