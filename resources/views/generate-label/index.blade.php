@extends('layouts.index')
@section('title', 'Generate Label For Internal Shipping - Modernist Look')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 offset-content">

                <div class="box box-primary">
                    <form class="form-horizontal" enctype="multipart/form-data" action="{{ route('label-generate') }}" method="post">
                        {{ csrf_field() }}
                        <div class="box-header with-border">
                            <div class="row">
                                <div class="col-md-8 col-md-offset-1"><h3>Internal Label Generater</h3></div>
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