@extends('layouts.index')
@section('title', 'Converters')

@section('content')
    <div class="container converters-list">
        <div class="row">
            <div class="col-md-12 offset-content">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="box-title">
                            <h1>Converters Information</h1>
                            <a href="{{route('converter-create')}}" class="btn btn-primary">Create</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>id</th>
                                <th>Name</th>
                                <th>Prefix</th>
                                <th>Service options</th>
                                <th>Battery packing</th>
                                <th>Battery type</th>
                                <th>Description</th>
                                <th>Description CN</th>
                                <th>Actions</th>
                            </tr>

                            </thead>
                            @if (count($converters) > 0)
                                <tbody>
                                @foreach($converters as $converter)
                                    <tr>
                                        <td>{{ $converter->id }}</td>
                                        <td>{{ $prettyNames[$converter->converter_type] }}</td>
                                        <td>{{ $converter->prefix }}</td>
                                        <td>{{ $converter->service_options }}</td>
                                        <td>{{ $converter->battery_packing }}</td>
                                        <td>{{ $converter->battery_type }}</td>
                                        <td>{{ $converter->description }}</td>
                                        <td>{{ $converter->description_cn }}</td>
                                        <td>
                                            <form action="{{route('converter-delete', ['id' => $converter->id])}}" method="post" class="form-inline form-in-table">
                                                {{method_field('delete')}}
                                                {!! csrf_field() !!}
                                                <button title="{{ 'Delete ' . $prettyNames[$converter->converter_type]}}"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                            </form>
                                            <a href="{{route('converter-update', $converter)}}" title="{{ 'Update ' . $prettyNames[$converter->converter_type]}}">
                                                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @else
                                <tbody>
                                <tr>
                                    <td colspan="8">Converters not found</td>
                                </tr>
                                </tbody>
                            @endif

                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
@stop