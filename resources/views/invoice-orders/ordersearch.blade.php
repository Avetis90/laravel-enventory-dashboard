@extends('layouts.index')
@section('title', 'Invoice Orders Datables - Modernist Look')

@section('content')
    <div class="container">
    <div class="row">
            <div class="box-header">
              <h2 class="box-title">Invoice Orders Status</h3>
            </div>
            <div class="box col-lg-12 offset-content table-responsive">
				<form method="POST" id="search-form" class="form-inline" role="form" _lpchecked="1">

					<div class="form-group">
						<label for="order_id">Order Id: </label>
						<input type="text" class="form-control" name="order_id" id="order_id" placeholder="search order">
					</div>
					<button type="submit" class="btn btn-default">Search</button>
				</form>
				<br />
			
                <table class="table table-striped table-bordered" cellspacing="0" id="invoiceorders-table">
                    <thead>
                        <tr>
                            <th>Order Id</th>
                            <th>Tracking Numbers</th>
                            <th>Sku</th>
                            <th>Weight</th>
                            <th>Product Cost</th>
                            <th>Shipping Cost</th>
                            <th>Total Cost</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <script>
    $(document).ready(function() {
        var invoiceTable = $('#invoiceorders-table').DataTable({
            processing: true,
            serverSide: true,
			pageLength: 50,
			ajax: {
				url: '{{route('invoice-orders-searchdata')}}',
				data: function (d) {
					d.order_id = $('input[name=order_id]').val();
				}
			},
            columns: [
                { data: 'order_id', name: 'order_id' },
                { data: 'track_num', name: 'track_num' },
                { data: 'sku', name: 'sku' },
                { data: 'weight', name: 'weight' },
                { data: 'product_cost', name: 'product_cost' },
                { data: 'shipping_cost', name: 'shipping_cost' },
                { data: 'total_cost', name: 'total_cost' },
                { data: 'status', name: 'status' },
            ],
            dom: "<'row'<'col-sm-3'l><'col-sm-9 text-right'B>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
            'copy', 'excel', 'pdf'
            ]
        } );
		


		$('#search-form').on('submit', function(e) {
			invoiceTable.draw();
			e.preventDefault();
		});
    });
    </script>
@stop
@section('headerscripts')
<script src="//cdn.jsdelivr.net/npm/datatables.net@1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/datatables.net-bs@1.10.16/js/dataTables.bootstrap.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/datatables.net-buttons@1.4.2/js/dataTables.buttons.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/datatables.net-buttons-bs@1.4.2/js/buttons.bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="//cdn.jsdelivr.net/npm/datatables.net-buttons@1.4.2/js/buttons.print.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/datatables.net-buttons@1.4.2/js/buttons.html5.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/datatables.net-buttons@1.4.2/js/buttons.flash.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/datatables.net-buttons@1.4.2/js/buttons.colVis.min.js"></script>
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/datatables.net-bs@1.10.16/css/dataTables.bootstrap.css">
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/datatables.net-buttons-bs@1.4.2/css/buttons.bootstrap.min.css">
@stop
