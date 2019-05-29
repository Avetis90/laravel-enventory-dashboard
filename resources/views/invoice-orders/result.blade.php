@extends('layouts.index')
@section('title', 'Invoice Orders - Modernist Look')

@section('content')
    <div class="container">
        <div class="row">
            <div class="box col-lg-12 offset-content">
				<div class="box-header">
				  <h3 class="box-title">Invoice Orders Status</h3>
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
								<th>Tracking Number</th>
							</tr>
						</thead>
						
                        <tbody>
						@foreach($duplicateOrders as $duplicateOrder)
							<tr>
								<td><span class="label label-danger">Duplicate</span></td>
								<td>{{ $duplicateOrder['order_id'] }}</td>
								<td>{{ $duplicateOrder['track_num'] }}</td>
							</tr>
						@endforeach
						@foreach($orders as $order)
							<tr>
								<td><span class="label label-success">Success</span></td>
								<td>{{ $order['order_id'] }}</td>
								<td>{{ $order['track_num'] }}</td>
							</tr>
						@endforeach
                        </tbody>
					</table>
                    
                </div>
				
            </div>
        </div>
    </div>
@stop