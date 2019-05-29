@extends('layouts.index')
@section('title', 'Check Changes')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 offset-content">

                <div class="box box-primary">
                    <form class="form-horizontal" enctype="multipart/form-data" action="{{ route('check-pdf-change') }}" method="post">
                        {{ csrf_field() }}
                        <div class="box-header with-border">
                            <div class="row">
                                <div class="col-md-6 col-md-offset-3"><h1>File changing</h1></div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group {{ $errors->has('file') ? 'has-error' : ''}}">
                                <label for="file-upload" class="col-sm-2 custom-file">File</label>
                                <div class="col-sm-10">
                                    <input type="file" name="file" id="file-upload" accespt="application/pdf"
                                           class="custom-file-input">
                                    <div class="help-block">{{ $errors->first('file') }}</div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('pattern') ? 'has-error' : ''}}">
                                <label for="pattern" class="col-sm-2 col-form-label">Element change</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="pattern" name="pattern"
                                           placeholder="Enter text needed to change">
                                    <div class="help-block">{{ $errors->first('pattern') }}</div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('size') ? 'has-error' : ''}}">
                                <label for="size" class="col-sm-2 col-form-label">Size</label>
                                <div class="col-sm-10">
                                    <input class="form-control" type="text" id="size" name="size"
                                           placeholder="Enter size what you needed">
                                    <div class="help-block">{{ $errors->first('size') }}</div>
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