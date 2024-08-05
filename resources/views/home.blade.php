@extends('layouts.main')
@section('title', __('Beranda'))
@section('content')
@foreach($pengumuman as $p)
<div class="widget-box">
  <div class="widget-header">
    <h5 class="widget-title">{{ $p->judul }}</h5>
  </div>
  <div class="widget-body">
    <div class="widget-main">
        <p class="alert alert-info">{{ $p->deskripsi }}</p>
    </div>
  </div>
</div>
@endforeach
@endsection
@section('custom-js')
<script>
    function view(url){
        window.open(url, "_blank");
    }
</script>
@endsection