@extends('admin.layouts.dashboard')
@section('content')
<div id="app"></div>
@if(!empty($childViews))
    @foreach($childViews as $key => $value )
        @include($value)
    @endforeach 
@endif
@endsection 