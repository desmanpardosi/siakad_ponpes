@extends('layouts.main')
@section('title', __('Input Nilai'))
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
      {{ $mapel->nama_hari }}
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
      {{ $mapel->jam }}
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
      {{ $mapel->mapel }}
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
      {{ $mapel->guru }}
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
      {{ $mapel->kelas_semester }}
    </div>
  </div>
  <div class="col-md-6">
    <div class="col-sm-5">
      Tahun Pelajaran
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $mapel->tahun_pelajaran }}
    </div>
  </div>
</div>
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Santri / Santri Wati</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Nama Santri / Santri Wati</th>
            <th>Nilai Angka</th>
            <th>Nilai Huruf</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($nilai) > 0)
          @foreach($nilai as $key => $m)
            <tr>
              <td class="text-center">{{ $nilai->firstItem() + $key }}</td>
              <td>{{ $m->nama_lengkap }}</td>
              <td>{{ $m->nilai }}</td>
              <td>{{ $m->nilai_huruf }}</td>
              <td class="text-center"><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($nilai[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
            </tr>
          @endforeach
        @else
            <tr>
                <td colspan="5">{{ __('Belum ada data') }}</td>
            </tr>
        @endif
        </tfoot>
      </table>
    </div>
  </div>
</div>
<div class="float-right">
  {{ $nilai->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Tambah Santri / Santri Wati</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('nilai.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="mapel" name="mapel" value="{{ $mapel->mapel_id }}">
            <div class="form-group row">
              <label for="santri" class="col-sm-4 col-form-label">Santri / Santri Wati</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="santri" name="santri"></select>
              </div>
            </div>
            <div class="form-group row">
              <label for="nilai" class="col-sm-4 col-form-label">Nilai</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nilai" name="nilai">
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
          <h4 class="blue bigger">Hapus Nilai Santri / Santri Wati</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('nilai.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus nilai Santri / Santri Wati bernama <b><span id="delete_name" class="bolder"></span></b>?</p>
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
    $("#button-save").text("Tambah Santri / Santri Wati");
    resetForm();
    getSantri();
  }

  function deleteData(data) {
    $("#delete_id").val(data.nilai_id);
    $("#delete_name").text(data.nama_lengkap);
  }

  function getSantri(){
      $.ajax({
          url: "{{ route('master.santri') }}",
          type: "GET",
          data: {"format": "json", "kelas": "{{ $mapel->kelas_id }}"},
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