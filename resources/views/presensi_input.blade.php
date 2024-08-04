@extends('layouts.main')
@section('title', __('Input Presensi'))
@section('custom-css')
@endsection
@section('content')
<div class="row">
  <div class="col-md-6">
    <div class="col-sm-5">
      Hari
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $jadwal->nama_hari }}
    </div>
  </div>
  <div class="col-md-6">
    <div class="col-sm-5">
      Jam Pelajaran
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $jadwal->jam }}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="col-sm-5">
      Mata Pelajaran
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $jadwal->mapel }}
    </div>
  </div>
  <div class="col-md-6">
    <div class="col-sm-5">
      Guru
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $jadwal->guru }}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-6">
    <div class="col-sm-5">
      Kelas
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $jadwal->kelas_semester }}
    </div>
  </div>
</div>
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Presensi</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Tanggal</th>
            <th>Nama Santri / Santri Wati</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($presensi) > 0)
          @foreach($presensi as $key => $m)
            <tr>
              <td class="text-center">{{ $presensi->firstItem() + $key }}</td>
              <td>{{ date("d/m/Y", strtotime($m->tgl_presensi)) }}</td>
              <td>{{ $m->nama_lengkap }}</td>
              <td class="text-center"><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($presensi[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
            </tr>
          @endforeach
        @else
            <tr>
                <td colspan="4">{{ __('Belum ada data') }}</td>
            </tr>
        @endif
        </tfoot>
      </table>
    </div>
  </div>
</div>
<div class="float-right">
  {{ $presensi->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Tambah Presensi</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('presensi.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="jadwal" name="jadwal" value="{{ $jadwal->jadwal_id }}">
            <div class="form-group row">
              <label for="tanggal" class="col-sm-4 col-form-label">Tanggal</label>
              <div class="col-sm-8">
                <input class="form-control date-picker" id="tanggal" name="tanggal"  type="text" data-date-format="yyyy-mm-dd" autocomplete="off"/>
              </div>
            </div>
            <div class="form-group row">
              <label for="santri" class="col-sm-4 col-form-label">Santri / Santri Wati</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="santri" name="santri"></select>
              </div>
            </div>
          </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
        <button id="button-save" type="button" class="btn btn-primary" onclick="document.getElementById('save').submit();">Simpan</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="del-data">
  <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="blue bigger">Hapus Presensi</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('presensi.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus Sntri / Santri Wati bernama <b><span id="delete_name" class="bolder"></span></b> dari presensi?</p>
              </div>
          </div>
          <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
              <button id="button-delete" type="button" class="btn btn-danger" onclick="$('#delete').submit();">Ya, hapus</button>
          </div>
      </div>
  </div>
</div>
@endsection
@section('custom-js')
<script src="/assets/js/bootstrap-datepicker.min.js"></script>
<script>
  $('.date-picker').datepicker({
    autoclose: true,
    todayHighlight: true
  })

  function resetForm(){
    $('#save').trigger("reset");
  }

  function addData(){
    $("#button-save").show();
    $("#button-save").text("Tambah Presensi");
    resetForm();
    getSantri();
  }

  function deleteData(data) {
    $("#delete_id").val(data.presensi_id);
    $("#delete_name").text(data.nama_lengkap);
  }

  function getSantri(){
      $.ajax({
          url: "{{ route('master.santri') }}",
          type: "GET",
          data: {"format": "json", "kelas": "{{ $jadwal->kelas_id }}"},
          dataType: "json",
          success:function(data) {
            $('#santri').empty();
            $('#santri').append('<option value="">.:: Pilih Santri / Santri Wati::.</option>');
            $.each(data, function(key, value) {
              $('#santri').append('<option value="'+ value.santri_id +'">'+value.nis+' - '+ value.nama_lengkap +'</option>');
            });
          }
      });
  }

  function view(url){
      window.open(url, "_self");
  }
</script>
@endsection