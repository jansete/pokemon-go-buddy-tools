@extends('layout.layout')

@section('content')
    @include('place.place')
@endsection

@section('javascript')
    @include('js.place')

    @include('js.place_experience')
@endsection