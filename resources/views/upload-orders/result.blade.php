@extends('layouts.index')
@section('title', 'Uploaded Orders - Modernist Look')

@section('content')
    <div class="container">
        <div class="row">
            <div class="box col-lg-12 offset-content">
				<div class="box-header">
				  <h3 class="box-title">Uploaded Orders Upload Status</h3>
				</div>
                <div class="box box-primary table-responsive">
				
                    <div class="box-body">
						{{ $message }}
					</div>
				
					<table id="table" class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Status</th>
								<th>Order #</th>
								<th>SKUs</th>
							</tr>
						</thead>
						
                        <tbody>
						@foreach($duplicateOrders as $duplicateOrder)
							<tr>
								<td><span class="label label-danger">Duplicate</span></td>
								<td>{{ $duplicateOrder['order_id'] }}</td>
								<td>{{ $duplicateOrder['skus'] }}</td>
							</tr>
						@endforeach
						@foreach($orders as $order)
							<tr>
								<td><span class="label label-success">Success</span></td>
								<td>{{ $order['order_id'] }}</td>
								<td>{{ $order['skus'] }}</td>
							</tr>
						@endforeach
                        </tbody>
					</table>
                    
                </div>
				
            </div>
        </div>
    </div>
@stop