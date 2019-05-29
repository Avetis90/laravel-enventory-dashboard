@extends('layouts.index')
@section('title', 'UBI Orders Uploaded - Modernist Look')

@section('content')
    <div class="container">
        <div class="row">
            <div class="box col-lg-12 offset-content">
				<div class="box-header">
				  <h3 class="box-title">UBI Upload Status</h3>

				  <div class="box-tools">
					<div class="input-group input-group-sm" style="width: 350px;">
						<button id="downloadPacklist" class="btn btn-primary" style="margin-right:10px">下载 Packlist</button>
						<button id="downloadPDF" class="btn btn-primary" style="margin-right:10px">下载 PDF</button>
						<button id="forcastOrders" class="btn btn-primary">预报成功单号</button>
						<script>
						var tableToExcel=new TableToExcel();
						document.getElementById('downloadPacklist').onclick=function(){
							var arr = [
								['单号', '追踪号', '货物清单'],
								@foreach($packlist as $item)
								['{{ $item['referenceNo'] }}', '{{ $item['trackingNo'] }}', '{{ $item['sku'] }}'],
								@endforeach
							]
							tableToExcel.render(arr);
						};
						@if($allOrderNumList)
							$(function() {
								$( "#downloadPDF" ).click(function() {
									window.location='{{route('ubi-download-pdf')}}?orders={{ urlencode($allOrderNumList) }}';
								});
							});
							$(function() {
								$( "#forcastOrders" ).click(function() {
									$.ajax({
										url : "{{route('ubi-forcast-orders')}}",
										type: "GET",
										data : "orders={{ urlencode($allOrderNumList) }}",
										success: function(data)
										{
											alert("预报成功: {{ $allOrderNumList }}");
										}
									});
								});
							});
						@endif
						</script>
					</div>
				  </div>
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