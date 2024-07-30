@extends('layouts.main')
@section('title', __('Beranda'))
@section('content')

@endsection
@section('custom-js')
<script>
    function view(url){
        window.open(url, "_blank");
    }
</script>
@endsection