@extends('layouts.index')
@section('title', 'Sets Rules')

@section('content')
    <div class="container converters-list">
        <div class="row">
            <div class="col-md-12 offset-content">
                <div class="box box-primary">
                    <form class="form-horizontal" enctype="multipart/form-data"
                          action="{{ route('set-rules-apply') }}"
                          method="post" autocomplete="off" novalidate
                    >
                        {{ csrf_field() }}
                        <div class="box-header with-border">
                            <div class="box-title">
                                <h3>Apply Set rules</h3>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="form-group {{ $errors->has('file') ? 'has-error' : ''}}">
                                <label for="file" class="col-md-4 custom-file">File</label>
                                <div class="col-md-8">
                                    <input type="file" name="file" id="file" class="custom-file-input">
                                    <div class="help-block">{{ $errors->first('file') }}</div>
                                </div>
                            </div>
                            <div class="form-group {{ $errors->has('set') ? 'has-error' : ''}}">
                                <label for="set" class="col-md-4 custom-file">Select Set rules</label>
                                <div class="col-md-8">
                                    <select name="set" id="set" class="form-control">
                                        <option value="">Select Set</option>
                                        @foreach($sets as $set)
                                            <option value="{{$set->id}}">{{$set->title}}</option>
                                        @endforeach
                                    </select>
                                    <div class="help-block">{{ $errors->first('set') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <div class="row">
                                <div class="col-md-4">
                                    <button class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 offset-content">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">
                            <h3>Manage Sets of rules</h3>
                            <a href="{{route('set-rules-create')}}" class="btn btn-primary">Create</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Name</th>
                                <th>Rules</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            @if (count($sets) > 0)
                                <tbody>
                                @foreach($sets as $set)
                                    <tr>
                                        <td>{{ $set->id }}</td>
                                        <td>
                                            <a href="{{route('rules-set', $set)}}"
                                               title="{{ 'Update set rules ' .  $set->title}}"
                                            >
                                                {{ $set->title }}
                                            </a>
											<br /><br />
											<p>
											<strong>Files Available:</strong><br />
											@foreach($setFiles[$set->id] as $i => $setConverterType)
												<a href="{{route('dl-file',[$set->id, $setConverterType])}}">{{$prettyNames[$setConverterType]}}</a> <small>({{$fileDates[$set->id][$i]}})</small><br /><br />
											@endforeach
											</p>
                                        </td>
                                        <td>
                                            <table class="table">
                                                <thead>
                                                <th>Country</th>
                                                <th>Converter</th>
                                                <th>Prefix</th>
                                                <th>Service Options</th>
                                                <th>Battery Packing</th>
                                                <th>Battery Type</th>
                                                <th>Description</th>
                                                <th>Description Cn</th>
                                                </thead>
                                                @if(count($set->rules) > 0)
                                                    <tbody>													
                                                    @foreach($set->rules as $rule)
                                                        <tr>
                                                            <td>{{ $rule->country }}</td>
                                                            <td>@if($rule->hasFile())
                                                                    <a href="{{route('get-file',[$rule])}}">{{$prettyNames[$rule->converter->converter_type]}}</a>
                                                                @else
                                                                    {{ $prettyNames[$rule->converter->converter_type] }}
                                                                @endif
                                                            </td>
                                                            <td>{{ $rule->converter->preffix }}</td>
                                                            <td>{{ $rule->service_options }}</td>
                                                            <td>{{ $rule->battery_packing }}</td>
                                                            <td>{{ $rule->battery_type }}</td>
                                                            <td>{{ $rule->description }}</td>
                                                            <td>{{ $rule->description_cn }}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                @else
                                                    <tbody>
                                                    <tr>
                                                        <td colspan="7">Rules not found</td>
                                                    </tr>
                                                    </tbody>
                                                @endif
                                            </table>
                                        </td>
                                        <td>
                                            <a href="{{route('set-rules-update', [$set])}}"
                                               title="{{ 'Update set rules ' . $set->title}}">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            <form action="{{ route('rules-set-delete', [$set]) }}"
                                                  method="post" class="form-inline form-in-table">
                                                {{method_field('delete')}}
                                                {!! csrf_field() !!}
                                                <button title="{{ 'Delete set rules ' . $set->title}}"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></button>
                                            </form>
                                        </td>
                                @endforeach
                                </tbody>
                            @else

                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop