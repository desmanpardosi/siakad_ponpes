@extends('layouts.main')
@section('title', __('Laporan'))
@section('custom-css')
  <link rel="stylesheet" href="/assets/css/datepicker.min.css" />
@endsection
@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="row">
      <div class="col-lg-12">
        <div class="widget-box widget-color-blue3">
          <div class="widget-header">
            <h4 class="widget-title lighter smaller">Permintaan Akun HIS / Perbaikan Data</h4>
          </div>
          <div class="widget-body">
            <div class="widget-main padding-8">
              <form role="form" id="search" target="_blank" method="post">
                @csrf
                <div class="input-group">
                  <span class="input-group-addon">
                    e-Form
                  </span>
                  <select class="form-control" name="eform">
                    <option value="0">Permintaan Akun HIS</option>
                    <option value="1">Permintaan Perbaikan Data</option>
                  </select>
                  <span class="input-group-addon">
                    ID Pengajuan #
                  </span>
                  <input type="number" class="form-control" placeholder="Masukkan ID Pengajuan" name="id">
                  <span class="input-group-btn">
                      <button type="submit" class="btn btn-primary btn-sm">
                          <span class="ace-icon fa fa-download icon-on-right bigger-110"></span>
                          Download
                      </button>
                  </span>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="row">
      <div class="col-lg-12">
        <div class="widget-box widget-color-blue3">
          <div class="widget-header">
            <h4 class="widget-title lighter smaller">Laporan Permintaan Akun HIS & Perbaikan Data</h4>
          </div>
          <div class="widget-body">
            <div class="widget-main padding-8">
              <form role="form" id="search" target="_blank" method="post">
                <input type="hidden" name="type" value="1"/>
                @csrf
                <div class="input-group">
                  <input class="form-control date-picker" id="tgl_awal" name="tgl_awal"  type="text" placeholder="Tanggal Awal" data-date-format="yyyy/mm/dd" autocomplete="off" value="{{ date('Y-m-01') }}"/>
                  <span class="input-group-addon">
                    s/d
                  </span>
                  <input class="form-control date-picker" id="tgl_akhir" name="tgl_akhir"  type="text" placeholder="Tanggal Akhir" data-date-format="yyyy/mm/dd" autocomplete="off" value="{{ date('Y-m-d') }}"/>
                  <span class="input-group-btn">
                      <button type="submit" class="btn btn-primary btn-sm">
                          <span class="ace-icon fa fa-download icon-on-right bigger-110"></span>
                          Download
                      </button>
                  </span>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('custom-js')
<script src="/assets/js/jquery.ui.touch-punch.min.js"></script>
<script src="/assets/js/markdown.min.js"></script>
<script src="/assets/js/bootstrap-markdown.min.js"></script>
<script src="/assets/js/jquery.hotkeys.min.js"></script>
<script src="/assets/js/bootstrap-wysiwyg.min.js"></script>
<script src="/assets/js/jquery.maskedinput.min.js"></script>
<script src="/assets/js/bootstrap-datepicker.min.js"></script>
<script>
    $('.date-picker').datepicker({
      autoclose: true,
      todayHighlight: true
    })
    function view(url){
        window.open(url, "_blank");
    }
</script>
@endsection