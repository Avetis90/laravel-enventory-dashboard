@extends('layouts.index')
@section('title', 'Rules of Set')

@section('content')
    <div class="container converters-list">
        <div class="row">
            <div class="col-md-12 offset-content">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">
                            <h3>Manage rule set #{{ $set->id }}:{{ $set->title }}</h3>
                            <a href="{{route('rule-create', $set)}}" class="btn btn-primary">Create</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Country</th>
                                <th>Converter</th>
                                <th>Service Options</th>
                                <th>Actions</th>
                            </tr>

                            </thead>
                            @if (count($set->rules) > 0)
                                <tbody>
                                @foreach($set->rules as $rule)
                                    <tr>
                                        <td>{{ $rule->id }}</td>
                                        <td>{{ $rule->country }}</td>
                                        <td>{{ $prettyNames[$rule->converter->converter_type] }}</td>
                                        <td>{{ $rule->service_options }}</td>
                                        <td>
                                            <a href="{{route('rule-update', [$set, $rule])}}"
                                               title="{{ 'Update rule ' . $rule->country}}">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                            <form action="{{ route('rule-delete', ['Set' => $set, 'ConverterRule' => $rule]) }}"
                                                  method="post" class="form-inline form-in-table">
                                                {{method_field('delete')}}
                                                {!! csrf_field() !!}
                                                <button title="{{ 'Delete rule ' . $rule->country}}"><i
                                                            class="fa fa-trash-o" aria-hidden="true"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @else
                                <tbody>
                                <tr>
                                    <td colspan="4">Sets of rules not found</td>
                                </tr>
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@stop