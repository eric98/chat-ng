@extends('adminlte::layouts.app')

@section('htmlheader_title')
    Statistics
@endsection


@section('main-content')
    <statistics-chart
            :messages="{{ json_encode($messages) }}"
            :total-users="{{ json_encode($totalUsers)}}"
            :users="{{json_encode($users)}}"
    ></statistics-chart>
@endsection
