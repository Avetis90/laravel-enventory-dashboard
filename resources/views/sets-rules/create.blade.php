@extends('layouts.index')
@section('title', 'Create Advanced converter Rule')

@section('content')
    <div class="container converter-create">
        <div class="row">
            <div class="col-md-12 offset-content">
                <div class="box box-primary">
                    <form class="form-horizontal"
                          action="{{ route('set-rules-create') }}"
                          method="post" autocomplete="off" novalidate>
                        {{ csrf_field() }}
                        <div class="box-header with-border">
                            <div class="box-title">
                                <h1>Rule for advanced converter</h1>
                            </div>
                        </div>

                        <div class="box-body">
                            {{--title--}}
                            <div class="form-group {{ $errors->has('title') ? 'has-error' : ''}}">
                                <label for="title" class="col-sm-2 custom-file">Name</label>
                                <div class="col-sm-4">
                                    <input name="title" id="title" class="form-control"
                                           value="{{ old('title') }}">
                                    <div class="help-block">{{ $errors->first('title') }}</div>
                                </div>
                            </div>
							
                            <div class="form-group {{ $errors->has('description') ? 'has-error' : ''}}">
                                <label for="description" class="col-sm-2 custom-file">Description</label>
                                <div class="col-sm-4">
                                    <textarea name="description" id="description" class="form-control" >{{ old('description') }}</textarea>
                                    <div class="help-block">{{ $errors->first('description') }}</div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('description_cn') ? 'has-error' : ''}}">
                                <label for="description_cn" class="col-sm-2 custom-file">Description CN</label>
                                <div class="col-sm-4">
                                    <textarea name="description_cn" id="description_cn" class="form-control">{{ old('description_cn') }}</textarea>
                                    <div class="help-block">{{ $errors->first('description_cn') }}</div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('battery_packing') ? 'has-error' : ''}}">
                                <label for="battery_packing" class="col-sm-2 custom-file">Battery packing</label>
                                <div class="col-sm-4">
                                    <input name="battery_packing" id="battery_packing" class="form-control" value="{{ old('battery_packing') }}">
                                    <div class="help-block">{{ $errors->first('battery_packing') }}</div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('battery_type') ? 'has-error' : ''}}">
                                <label for="battery_type" class="col-sm-2 custom-file">Battery type</label>
                                <div class="col-sm-4">
                                    <input name="battery_type" id="battery_type" class="form-control" value="{{ old('battery_type') }}">
                                    <div class="help-block">{{ $errors->first('battery_type') }}</div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('prefix') ? 'has-error' : ''}}" >
                                <label for="prefix" class="col-sm-2 custom-file">Prefix</label>
                                <div class="col-sm-4">
                                    <input name="prefix" id="prefix" class="form-control" value="{{ old('prefix') }}">
                                    <div class="help-block">{{ $errors->first('prefix') }}</div>
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