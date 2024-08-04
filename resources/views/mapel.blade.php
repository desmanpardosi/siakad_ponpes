@extends('layouts.main')
@section('title', __('Master Mata Pelajaran'))
@section('custom-css')
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Mata Pelajaran</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Mata Pelajaran') }}</th>
            <th>{{ __('Kelas / Semester') }}</th>
            <th>{{ __('Guru') }}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($mapel) > 0)
          @foreach($mapel as $key => $m)
            <tr>
              <td class="text-center">{{ $mapel->firstItem() + $key }}</td>
              <td>{{ $m->mapel }}</td>
              <td>{{ $m->kelas_semester }}</td>
              <td>{{ $m->guru }}</td>
              <td class="text-center"><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($mapel[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
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
  {{ $mapel->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Tambah Mata Pelajaran</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.mapel.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="mapel_id" name="mapel_id">
            <div class="form-group row">
              <label for="mapel" class="col-sm-4 col-form-label">Mata Pelajaran</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="mapel" name="mapel">
              </div>
            </div>
            <div class="form-group row">
              <label for="kelas" class="col-sm-4 col-form-label">Kelas</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="kelas" name="kelas"></select>
              </div>
            </div>
            <div class="form-group row">
              <label for="guru" class="col-sm-4 col-form-label">Guru</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="guru" name="guru"></select>
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
          <h4 class="blue bigger">Hapus Mata Pelajaran</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.mapel.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus <b><span id="delete_name" class="bolder"></span></b> dari daftar mata pelajaran?</p>
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
<script>
  function resetForm(){
    $('#save').trigger("reset");
  }

  function addData(){
    $("#button-save").show();
    $("#button-save").text("Tambah");
    resetForm();
    getKelas();
    getGuru();
  }

  function deleteData(data) {
    $("#delete_id").val(data.mapel_id);
    $("#delete_name").text(data.mapel);
  }

  function getKelas(val){
      $.ajax({
          url: "{{ route('master.kelas') }}",
          type: "GET",
          data: {"format": "json"},
          dataType: "json",
          success:function(data) {
            $('#kelas').empty();
            $('#kelas').append('<option value="">.:: Pilih Kelas ::.</option>');
            $.each(data, function(key, value) {
              $('#kelas').append('<option value="'+ value.kelas_id +'">'+ value.kelas_semester +'</option>');
            });
          }
      });
  }

  function getGuru(val){
      $.ajax({
          url: "{{ route('master.users') }}",
          type: "GET",
          data: {"format": "json", "role": 2},
          dataType: "json",
          success:function(data) {
            $('#guru').empty();
            $('#guru').append('<option value="">.:: Pilih Guru ::.</option>');
            $.each(data, function(key, value) {
              $('#guru').append('<option value="'+ value.id +'">'+ value.name +'</option>');
            });
          }
      });
  }

  function view(url){
      window.open(url, "_blank");
  }
</script>
@endsection