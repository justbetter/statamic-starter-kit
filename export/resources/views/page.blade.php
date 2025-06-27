@extends('layouts.app')
@section('content')
    @foreach ($page_builder as $set)
        @includeIf('page_builder.' . $set['type'], $set)
    @endforeach
@endsection
