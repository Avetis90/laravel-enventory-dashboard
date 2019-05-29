@extends('layouts.index')
@section('title', 'Tracking')

@section('content')
    <div class="container-fluid container-tracking">
        <div class="row">
            <div class="col-md-3 offset-content">

                <div class="box box-primary">
                    <form class="form-horizontal" action="{{ route('tracking') }}" method="post" autocomplete="off"
                          novalidate>
                        {{ csrf_field() }}
                        <div class="box-header with-border">
                            <div class="row">
                                <div class="col-md-6 col-md-offset-3"><h1>Tracking Number</h1></div>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group {{ $errors->has('tracker') ? 'has-error' : ''}}">
                                <label for="trackers" class="col-sm-4">Tracker Type</label>
                                <div class="col-sm-8">
                                    <select name="tracker" id="trackers" class="form-control">
                                        <option value="">Select tracker type</option>
                                        @foreach ($trackers as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">{{ $errors->first('tracker') }}</div>
                                </div>
                            </div>
                            <div class="form-group  {{ $errors->has('tracking_number') ? 'has-error' : ''}}">
                                <label for="tracking_number" class="col-sm-4">Tracking Number</label>
                                <div class="col-sm-8">
                                    <input name="tracking_number" id="tracking_number" class="form-control">
                                    <div class="help-block">{{ $errors->first('tracking_number') }}</div>
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
            <div class="col-md-9 offset-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Tracking Info</h3>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Data</th>
                                        <th>Info</th>
                                        <th>Details</th>
                                    </tr>
                                    </thead>
                                    @if(!$content['content'])
                                        <tbody>
                                        <tr>
                                            <td colspan="3">Info does not exist</td>
                                        </tr>
                                        </tbody>
                                    @endif
                                    @if($content['content'])
                                        <tbody>
                                        @foreach($content['content'] as $key => $item)
                                        <tr>
                                            <td>{{ ++$key }}</td>
                                            <td>{{ $item['Date'] }}</td>
                                            <td>{{ $item['StatusDescription'] }}</td>
                                            <td>{{ $item['Details'] }}</td>
                                        </tr>
                                        @endforeach
                                        </tbody>

                                    @endif
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop