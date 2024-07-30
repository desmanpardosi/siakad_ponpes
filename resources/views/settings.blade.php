@extends('layouts.main')
@section('title', __('Pengaturan'))
@section('custom-css')

@endsection
@section('content')
<div class="row">
  <div class="col-sm-6">
    <div class="widget-box">
      <div class="widget-header">
        <h5 class="widget-title">
            <i class="ace-icon fa fa-key"></i>
            Ubah Password
        </h5>
        <div class="widget-toolbar">
          <button type="button" class="btn btn-xs btn-primary" onclick="$('#save').submit();"><i class="fa fa-save"></i> Simpan</button>
        </div>
      </div>
      <div class="widget-body">
        <div class="widget-main">
          <form role="form" id="save" action="{{ route('settings.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
                <label for="pass" class="col-sm-4 col-form-label">Password Lama</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="pass" name="pass">
                </div>
            </div>
            <div class="form-group row">
                <label for="newpass" class="col-sm-4 col-form-label">Password Baru</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="newpass" name="newpass">
                </div>
            </div>
            <div class="form-group row">
                <label for="newpass_confirmation" class="col-sm-4 col-form-label">Konfirmasi Password</label>
                <div class="col-sm-8">
                    <input type="password" class="form-control" id="newpass_confirmation" name="newpass_confirmation">
                </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection