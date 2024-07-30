@extends('layouts.main')
@section('title', '')
@section('custom-css')
@endsection
@section('content')
@if($data['status'] == "success")
<div class="alert alert-success">
  {{ $data['message'] }}
</div>
@elseif($data['status'] == "error")
<div class="alert alert-danger">
  {{ $data['message'] }}
</div>
@else
<div class="alert alert-info">
  {{ $data['message'] }}
</div>
@endif
@endsection
@section('custom-js')

@endsection