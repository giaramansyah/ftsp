@extends('layouts/main')
@section('title', $header)
@section('content')
<div class="container-fluid">
  @if($is_note)
    @include('contents.home.partials.note')
  @endif
  @if($is_general)
    @include('contents.home.partials.general')
  @endif
</div>
@endsection