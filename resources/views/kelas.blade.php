@extends('layouts.main')
@section('title', __('Master Kelas / Semester'))
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Kelas / Semester</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Kelas / Semester</th>
            <th>Jumlah Santri / Santri Wati</th>
            <th>Tgl. Buat</th>
            <th>User</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($kelas) > 0)
          @foreach($kelas as $key => $m)
            <tr>
              <td class="text-center">{{ $kelas->firstItem() + $key }}</td>
              <td>{{ $m->kelas_semester }}</td>
              <td class="text-center"><a href="{{ route('master.santri') }}?kelas={{ $m->kelas_id }}">{{ $m->jumlah_santri }}</a></td>
              <td>{{ date("d/m/Y H:i:s", strtotime($m->tgl_buat)) }}</td>
              <td>{{ $m->user_buat }}</td>
              <td class="text-center"><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($kelas[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
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
  {{ $kelas->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Tambah Kelas / Ruangan</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.kelas.save') }}" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="kelas_id" name="kelas_id">
              <div class="form-group row">
                <label for="kelas_semester" class="col-sm-4 col-form-label">Kelas / Semester</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="kelas_semester" name="kelas_semester">
                </div>
              </div>
          </form>
          <div id="note"></div>
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
          <h4 class="blue bigger">Hapus Kelas / Semester</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.kelas.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus <b><span id="delete_name" class="bolder"></span></b> dari daftar ruangan?</p>
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
  }

  function deleteData(data) {
    $("#delete_id").val(data.kelas_id);
    $("#delete_name").text(data.kelas_semester);
  }

  function view(url){
      window.open(url, "_blank");
  }
</script>
@endsection