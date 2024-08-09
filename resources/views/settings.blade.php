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
            Pengaturan
        </h5>
        <div class="widget-toolbar">
          <button type="button" class="btn btn-xs btn-primary" onclick="$('#save-setting').submit();"><i class="fa fa-save"></i> Simpan</button>
        </div>
      </div>
      <div class="widget-body">
        <div class="widget-main">
          <form role="form" id="save-setting" action="{{ route('settings.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="form-control" id="type" name="type" value="1">
            <div class="form-group row">
                <label for="nama_yayasan" class="col-sm-4 col-form-label">Nama Yayasan</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="nama_yayasan" name="nama_yayasan" value="{{ $nama_yayasan }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="ketua_yayasan" class="col-sm-4 col-form-label">Nama Ketua Yayasan</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="ketua_yayasan" name="ketua_yayasan" value="{{ $ketua_yayasan }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="kota" class="col-sm-4 col-form-label">Nama Kota/Kabupaten</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="kota" name="kota" value="{{ $kota }}">
                </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-sm-6">
    <div class="widget-box">
      <div class="widget-header">
        <h5 class="widget-title">
            <i class="ace-icon fa fa-key"></i>
            Ubah Password
        </h5>
        <div class="widget-toolbar">
          <button type="button" class="btn btn-xs btn-primary" onclick="$('#save-pass').submit();"><i class="fa fa-save"></i> Simpan</button>
        </div>
      </div>
      <div class="widget-body">
        <div class="widget-main">
          <form role="form" id="save-pass" action="{{ route('settings.save') }}" method="post" enctype="multipart/form-data">
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