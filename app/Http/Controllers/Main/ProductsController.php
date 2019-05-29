<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;

use App\Models\Products;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Excel;

use \Exception;

class ProductsController extends Controller
{
    public function index()
    {
        return view('products.index');
    }
    
    public function getProductsData(request $request)
    {
		$products = Products::select(['id', 'vendor_sku', 'our_sku', 'price', 'weight']);

        //return datatables(InvoiceOrders::take(20)->get())->toJson();
		return datatables()->of($products)
				->addColumn('action', function ($products) {
					return '<a href="/products/update/'.$products->id.'" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a> <a href="/products/delete/'.$products->id.'" onclick="return confirm_delete()" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-remove"></i> Del</a>';
				})
				->filter(function ($query) use ($request) {
					if ($request->has('vendor_sku')) {
						$query->where('vendor_sku', 'like', "%{$request->get('vendor_sku')}%");
					} elseif ($request->has('our_sku')) {
						$query->where('our_sku', 'like', "%{$request->get('our_sku')}%");
					}
				})
				->toJson();

    }
	
	public function create(Request $request)
	{
		if ($request->isMethod('post')) {
			$data = $this->validate($request, [
				'vendor_sku'=>'required',
				'our_sku'=> 'required',
				'price'=> 'required'
			]);

            if (!Products::create($request->all())) {
                return redirect('products-create')->with('error', 'Record not saved');
            }
            return redirect('products')->with('success', 'Record successfully created');
		}
		return view('products.create');
	}
	
    public function update($product_id, Request $request) {
		$model = new Products;
		$model = $model->find($product_id);

        if ($request->isMethod('post')) {
            $this->validate($request, [
				'vendor_sku'=>'required',
				'our_sku'=> 'required',
				'price'=> 'required'
			]);
			
            $model->fill($request->all());
            if (!$model->save()) {
                return redirect('products-update')->with('error', 'Converter update has failed');
            }
            return redirect('products')->with('success', 'Record successfully updated');
        }
		
        return view('products.update', [
            'model' => $model
        ]);
    }

    public function delete($product_id) {
		$model = new Products;
		$model = $model->find($product_id);

        if (!$model->delete()) {
            return redirect('products')->with('error', 'Converter deleted has failed');
        };
        return redirect('products')->with('success', 'Converter successfully deleted');
    }
}