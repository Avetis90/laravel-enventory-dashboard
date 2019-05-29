@extends('layouts.index')
@section('title', 'Product Update')

@section('content')
    <div class="container product-update">
        <div class="row">
            <div class="col-md-12 offset-content">
                <div class="box box-primary">
                    <form class="form-horizontal" enctype="multipart/form-data" action="{{ route('products-update', $model) }}"
                          method="post" autocomplete="off" novalidate>

                        <div class="box-header with-border">
                            <div class="box-title">
                                <h1>Product Update {{ $model->our_sku }}</h1>
                            </div>
                        </div>

                        <div class="box-body">
                            {{ csrf_field() }}
                            <div class="form-group {{ $errors->has('our_sku') ? 'has-error' : ''}}">
                                <label for="our_sku" class="col-sm-2 custom-file">Our SKU</label>
                                <div class="col-sm-4">
                                    <input name="our_sku" id="our_sku" class="form-control" value="{{ old('our_sku') ?: $model->our_sku }}">
                                    <div class="help-block">{{ $errors->first('our_sku') }}</div>
                                </div>
                            </div>
							
                            <div class="form-group {{ $errors->has('vendor_sku') ? 'has-error' : ''}}">
                                <label for="vendor_sku" class="col-sm-2 custom-file">Vendor SKU</label>
                                <div class="col-sm-4">
                                    <input name="vendor_sku" id="vendor_sku" class="form-control" value="{{ old('vendor_sku') ?: $model->vendor_sku }}">
                                    <div class="help-block">{{ $errors->first('vendor_sku') }}</div>
                                </div>
                            </div>
							
                            <div class="form-group {{ $errors->has('price') ? 'has-error' : ''}}">
                                <label for="price" class="col-sm-2 custom-file">Price</label>
                                <div class="col-sm-4">
                                    <input name="price" id="price" class="form-control" value="{{ old('price') ?: $model->price }}">
                                    <div class="help-block">{{ $errors->first('price') }}</div>
                                </div>
                            </div>

                        </div>

                        <div class="box-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary">Update</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop