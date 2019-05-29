@extends('layouts.index')
@section('title', 'Invoice Orders - Modernist Look')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 offset-content">


                <div class="box box-primary">
                    <form class="form-horizontal" enctype="multipart/form-data" action="{{ route('invoice-orders-upload') }}" method="post">
                        {{ csrf_field() }}
                        <div class="box-header with-border">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-1"><h3>Invoice Orders Upload</h3></div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12" style="text-align:center">
									<div class="form-group {{ $errors->has('file') ? 'has-error' : ''}}">
										<label for="file" class="col-sm-2 custom-file">File</label>
										<div class="col-sm-6">
											<input type="file" name="file" id="file" class="custom-file-input">
											<div class="help-block">{{ $errors->first('file') }}</div>
										</div>
									</div>
                                    
                                    <div class="form-group {{ $errors->has('prefix') ? 'has-error' : ''}}" >
                                        <label for="prefix" class="col-sm-2 custom-file">Prefix</label>
                                        <div class="col-sm-4">
                                            <input name="prefix" id="prefix" class="form-control" value="{{ old('prefix') }}">
                                            <div class="help-block">{{ $errors->first('prefix') }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group {{ $errors->has('format') ? 'has-error' : ''}}">
                                        <label for="format" class="col-sm-2 custom-file">Format</label>
                                        <div class="col-sm-4">
                                            <select name="format" id="format" class="form-control">
                                                <option value="ow">OneWorld</option>
                                                <option value="ubi">UBI</option>
                                                <option value="pfc">PFC</option>
                                                <option value="wyt">WYT/MTrust</option>
                                            </select>
                                            <div class="help-block">{{ $errors->first('format') }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
                                        <label for="status" class="col-sm-2 custom-file">Status</label>
                                        <div class="col-sm-4">
                                            <select name="status" id="status" class="form-control">
                                                <option value="unbilled">Unbilled</option>
                                                <option value="invoiced">Invoiced</option>
                                            </select>
                                            <div class="help-block">{{ $errors->first('status') }}</div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group {{ $errors->has('parse_prefix') ? 'has-error' : ''}}" >
                                        <label for="parse_prefix" class="col-sm-2 custom-file">Parse Only with Prefix (use comma, no space)</label>
                                        <div class="col-sm-4">
                                            <input name="parse_prefix" id="parse_prefix" class="form-control" value="{{ old('parse_prefix') }}">
                                            <div class="help-block">{{ $errors->first('parse_prefix') }}</div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <div class="row">
                                <div class="col-md-12 text-right">
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