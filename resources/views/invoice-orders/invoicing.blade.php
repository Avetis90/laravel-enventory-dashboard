@extends('layouts.index')
@section('title', 'Invoicing - Modernist Look')

@section('content')
    <div class="container">
    <div class="row">
            <div class="box-header">
              <h2 class="box-title">Invoice Orders Status</h3>
            </div>
            <div class="box col-lg-12 offset-content table-responsive">
                <p><button id="downloadSAXls" onclick="downloadXls('SA')" class="btn btn-primary">Download Invative Fulfillment Invoices</button></p>
                <p><button id="downloadMZXls" onclick="downloadXls('MZ')" class="btn btn-primary">Download MilkyZen Fulfillment Invoices</button></p>
				
				<script>
                    function downloadXls(prefix){
                        window.location='{{route('invoicing-xls')}}?prefix=' + prefix;
                    }
				</script>
            </div>
        </div>
    </div>
@stop
