@extends('layouts.main')
@section('title', __('Cek No. Kartu BPJS'))
@section('content')
<div class="row">
  <div class="col-lg-12 col-12">
      <form role="form" method="get">
        <div class="input-group">
          <span class="input-group-addon">
            No. RM
          </span>
          <input type="number" class="form-control" placeholder="Masukkan No. RM" name="no_rm">
          <span class="input-group-btn">
              <button type="submit" class="btn btn-primary btn-sm">
                  <span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
                  Cek
              </button>
          </span>
        </div>
      </form>
  </div>
</div>
@if(!empty($no_rm))
<div class="space-12"></div>
<div class="row">
  <div class="col-12">
    <div class="widget-box">
      <div class="widget-header"></div>
      <div class="widget-body">
        <div class="widget-main no-padding">
          <table id="table" class="table table-striped table-bordered table-hover">
            <thead>
              <tr>
                <th>No. RM</th>
                <th>Nama</th>
                <th>No. Kartu BPJS</th>
              </tr>
            </thead>
            <tbody>
            @if($check != false)
            <tr>
                <td>{{ $no_rm }}</td>
                <td>{{ $check->Nama }}</td>
                <td onclick="copy('{{ $check->NomerPolis }}')">{{ $check->NomerPolis }}</td>
            </tr>
            @else
            <tr>
                <td class="text-center" colspan="3">Tidak ditemukan!</td>
            </tr>
            @endif
            </tfoot>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endif
@endsection
@section('custom-js')
<script>
  function copy(text) {
      var sampleTextarea = document.createElement("textarea");
      document.body.appendChild(sampleTextarea);
      sampleTextarea.value = text;
      sampleTextarea.select();
      document.execCommand("copy");
      document.body.removeChild(sampleTextarea);
      alert("No. Kartu BPJS "+text+" berhasil di-copy.");
  }
</script>
@endsection