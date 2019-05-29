@extends('layouts.index')
@section('title', 'Template Convert Status - Modernist Look')

@section('content')
    <div class="container">
        <div class="row">
            <div class="box col-lg-12 offset-content">
				<div class="box-header">
				  <h3 class="box-title">Convert Status</h3>
				</div>
                <div class="box box-primary table-responsive">
					<table id="table" class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Status</th>
								<th>Order #</th>
								<th>Tracking Number</th>
								<th>Message</th>
							</tr>
						</thead>
						
                        <tbody>
						@foreach($responses as $response)
							@if ($response->status == "Failed")
							<tr>
								<td><span class="label label-danger">{{ $response->status }}</span></td>
								<td>{{ $response->referenceNo }}</td>
								<td>{{ $response->trackingNo }}</td>
								<td>{{ $response->message }}</td>
							</tr>
							@else
							<tr>
								<td><span class="label label-success">{{ $response->status }}</span></td>
								<td>{{ $response->referenceNo }}</td>
								<td>{{ $response->trackingNo }}</td>
								<td>{{ $response->message }}</td>
							</tr>
							@endif
						@endforeach
                        </tbody>
					</table>
                </div>
				
            </div>
        </div>
    </div>
@stop