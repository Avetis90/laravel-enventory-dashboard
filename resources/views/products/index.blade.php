@extends('layouts.index')
@section('title', 'Products Datables - Modernist Look')

@section('content')
    <div class="container">
    <div class="row">
            <div class="box-header">
              <h2 class="box-title">Products</h3>
            </div>
            <div class="box col-lg-12 offset-content table-responsive">
					<a href="{{route('products-create')}}" class="btn btn-success" style="float:right;width:150px">Add Product</a>
				<form method="POST" id="search-form" class="form-inline" role="form" _lpchecked="1">

					<div class="form-group">
						<label for="our_sku">Our Sku: </label>
						<input type="text" class="form-control" name="our_sku" id="our_sku" placeholder="search order">
					</div>
					<button type="submit" class="btn btn-default">Search</button>
				</form>
				<br />
			
                <table class="table table-striped table-bordered" cellspacing="0" id="products-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Our SKU</th>
                            <th>Vendor SKU</th>
                            <th>Fulfillment Price</th>
                            <th>Wholesale Price</th>
                            <th>Price Difference</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <script>
	function confirm_delete() {
	  return confirm('are you sure?');
	}
    $(document).ready(function() {
        var productsTable = $('#products-table').DataTable({
            processing: true,
            serverSide: true,
			pageLength: 50,
			ajax: {
				url: '{{route('products-data')}}',
				data: function (d) {
					d.our_sku = $('input[name=our_sku]').val();
				}
			},
            columns: [
                { data: 'id', name: 'id' },
                { data: 'our_sku', name: 'our_sku' },
                { data: 'vendor_sku', name: 'vendor_sku' },
                { data: 'price', name: 'price' },
                { data: 'wholesale_price', name: 'wholesale_price' },
                { data: 'price_dif', name: 'price_dif', orderable: false },
				{ data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            dom: "<'row'<'col-sm-3'l><'col-sm-9 text-right'B>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            buttons: [
            'copy', 'excel', 'pdf'
            ]
        } );
		


		$('#search-form').on('submit', function(e) {
			productsTable.draw();
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
