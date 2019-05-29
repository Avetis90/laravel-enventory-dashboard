<?php
    
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
	protected $fillable = [
	'vendor_sku',
	'our_sku',
	'price',
	'weight',
	];
	
	public $timestamps = false;
	protected $appends = ['wholesale_price', 'price_dif'];
	
	public function getRouteKey()
	{
		return $this->id;
	}
	
	public function getWholesalePriceAttribute()
	{
		$db_ext = \DB::connection('pgsql');
		//$products = $db_ext->table('product')->get();$this->getAttribute('sku')
		$product_price = $db_ext->table('product')->where('system_sku', $this->getAttribute('our_sku'))->value('price');
		
		return number_format($product_price, 2);
	}
	
	public function getPriceDifAttribute()
	{
		$total_dif = $this->getAttribute('price') - $this->getAttribute('wholesale_price');
		return $total_dif;
		
	}
	
}