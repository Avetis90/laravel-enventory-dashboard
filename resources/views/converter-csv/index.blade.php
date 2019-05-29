@extends('layouts.index')
@section('title', 'Convert CSV')

@section('content')
    <div class="container container-csv-convert">
        <div class="row">
            <div class="col-md-12 offset-content">

                <div class="box box-primary">
                    <form class="form-horizontal" enctype="multipart/form-data" action="{{ route('converter-csv-change') }}" method="post" autocomplete="off" novalidate>
                        {{ csrf_field() }}
                        <div class="box-header with-border">
                            <div class="row">
                                <div class="col-md-6 col-md-offset-3"><h1>Convert File</h1></div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group {{ $errors->has('parser') ? 'has-error' : ''}}">
                                <label for="parser" class="col-sm-2 custom-file">Parser Type</label>
                                <div class="col-sm-4">
                                    <select name="parser" id="parser" class="form-control">
                                        <option value="">Select parser type</option>
                                        @foreach ($parsers as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">{{ $errors->first('parser') }}</div>
                                </div>
                            </div>

                            <div class="form-group type type-ubiCa type-ubiAu type-ubiEu type-ubiNz type-ubiUs type-owEuDirectLine">
                                <label for="description" class="col-sm-2 custom-file">Description</label>
                                <div class="col-sm-4">
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="form-group type type-ubiEu type-ubiUs type-owEuDirectLine">
                                <label for="description_cn" class="col-sm-2 custom-file">Description CN</label>
                                <div class="col-sm-4">
                                    <textarea name="description_cn" id="description_cn" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="form-group type type-ubiNz">
                                <label for="service_options" class="col-sm-2 custom-file">Service options</label>
                                <div class="col-sm-4">
                                    <input name="service_options" id="service_options" class="form-control">
                                </div>
                            </div>

                            <div class="form-group type type-ubiNz">
                                <label for="battery_packing" class="col-sm-2 custom-file">Battery packing</label>
                                <div class="col-sm-4">
                                    <input name="battery_packing" id="battery_packing" class="form-control">
                                </div>
                            </div>

                            <div class="form-group type type-ubiNz">
                                <label for="battery_type" class="col-sm-2 custom-file">Battery type</label>
                                <div class="col-sm-4">
                                    <input name="battery_type" id="battery_type" class="form-control">
                                </div>
                            </div>

                            <div class="form-group type type-ubiUs type-owEuDirectLine">
                                <label for="prefix" class="col-sm-2 custom-file">Prefix</label>
                                <div class="col-sm-4">
                                    <input name="prefix" id="prefix" class="form-control">
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('file') ? 'has-error' : ''}}">
                                <label for="file" class="col-sm-2 custom-file">File</label>
                                <div class="col-sm-10">
                                    <input type="file" name="file" id="file" class="custom-file-input">
                                    <div class="help-block">{{ $errors->first('file') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop