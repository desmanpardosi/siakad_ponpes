@extends('layouts.main')
@section('title', __('Rekap'))
@section('content')
<div class="row">
  <div class="col-lg-12 col-12">
      <form role="form" id="search" target="_blank" method="post">
        @csrf
        <div class="input-group">
          <span class="input-group-addon"><b>Rekap</b></span>
          <select class="form-control" name="eform">
            <option value="0">Permintaan Akun HIS</option>
            <option value="1">Permintaan Perbaikan Data</option>
          </select>
          <span class="input-group-addon"><b>Tanggal</b></span>
          <input class="form-control date-picker" id="tgl_perkiraan_selesai" name="tgl_perkiraan_selesai" type="text" data-date-format="dd-mm-yyyy" autocomplete="off"/>
          <span class="input-group-addon">s/d</span>
          <input class="form-control date-picker" id="tgl_perkiraan_selesai2" name="tgl_perkiraan_selesai2" type="text" data-date-format="dd-mm-yyyy" autocomplete="off"/>
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
@if(!empty(Request::get('search')) || !empty(Request::get('category')))
<div class="space-12"></div>
<div class="widget-box">
  <div class="widget-header">
    @if(!empty(Request::get('search')) && empty(Request::get('category')))
    <h5 class="widget-title">Hasil Pencarian "<b>{{ Request::get('search') }}</b>"</h5>
    @endif
    @if(!empty(Request::get('category')) && empty(Request::get('search')))
    <h5 class="widget-title">Kategori <b>{{ $category }}</b></h5>
    @endif
    @if(!empty(Request::get('category')) && !empty(Request::get('search')))
    <h5 class="widget-title">Hasil Pencarian "<b>{{ Request::get('search') }}</b>" di kategori <b>{{ $category }}</b></h5>
    @endif
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Judul') }}</th>
            <th>{{ __('Kategori / Sub Kategori') }}</th>
            <th>{{ __('Aksi') }}</th>
          </tr>
        </thead>
        <tbody>
        @if(count($ebook) > 0)
        @foreach($ebook as $key => $m)
        <tr>
            <td class="text-center">{{ $ebook->firstItem() + $key }}</td>
            <td>{{ $m->title }}</td>
            @if(!empty($m->subcategory))
            <td>{{ $m->category }} | {{ $m->subcategory }}</td>
            @else
            <td>{{ $m->category }}</td>
            @endif
            <td class="text-center"><button title="Baca" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#view-data" onclick="view('{{ route("ebook") }}/{{ $m->id_ebook }}')"><i class="fa fa-eye"></i></button></td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="4">{{ __('e-Book belum tersedia.') }}</td>
        </tr>
        @endif
        </tfoot>
      </table>
    </div>
  </div>
</div>
@if(count($ebook) > 0)
<div class="float-right">
  {{ $ebook->appends(request()->query())->links("pagination::bootstrap-4") }}
</div>
@endif
@endif
@endsection
@section('custom-js')
<script>
    function view(url){
        window.open(url, "_blank");
    }
</script>
@endsection