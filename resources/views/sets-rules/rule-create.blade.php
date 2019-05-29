@extends('layouts.index')
@section('title', 'Create Set Rule')

@section('content')
    <div class="container converter-create">
        <div class="row">
            <div class="col-md-12 offset-content">
                <div class="box box-primary">
                    <form class="form-horizontal"
                          action="{{ route('rule-create', $set) }}"
                          method="post" autocomplete="off" novalidate>
                        {{ csrf_field() }}
                        <div class="box-header with-border">
                            <div class="box-title">
                                <h1>Rule for set {{$set->title}}</h1>
                            </div>
                        </div>

                        <div class="box-body">
                            {{--converters--}}
                            <div class="form-group {{ $errors->has('converter_id') ? 'has-error' : ''}}">
                                <label for="converter_type" class="col-sm-2 custom-file">Parser Type</label>
                                <div class="col-sm-4">
                                    <select name="converter_id" id="converter_id" class="form-control">
                                        <option value="">Select parser type</option>
                                        @foreach ($converters as $item)
                                            <option value="{{ $item->id }}" {{ old('converter_id')  === $item->id ? 'selected' : "" }}>{{ $prettyNames[$item->converter_type] }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">{{ $errors->first('converter_id') }}</div>
                                </div>
                            </div>

                            {{--country--}}
                            <div class="form-group {{ $errors->has('country') ? 'has-error' : ''}}">
                                <label for="country" class="col-sm-2 custom-file">Country</label>
                                <div class="col-sm-4">
                                    <input name="country" id="country" class="form-control"
                                           value="{{ old('country') }}">
                                    <div class="help-block">{{ $errors->first('country') }}</div>
                                </div>
                            </div>

                            <div class="form-group {{ $errors->has('service_options') ? 'has-error' : ''}}">
                                <label for="service_options" class="col-sm-2 custom-file">Service options</label>
                                <div class="col-sm-4">
                                    <input name="service_options" id="service_options" class="form-control" value="{{ old('service_options') }}">
                                    <div class="help-block">{{ $errors->first('service_options') }}</div>
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