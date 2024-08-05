@extends('layouts.main')
@section('title', __('Master Jam Pelajaran'))
@section('custom-css')
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Tahun Pelajaran</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Tahun Pelajaran') }}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($tp) > 0)
          @foreach($tp as $key => $m)
            <tr>
              <td class="text-center">{{ $tp->firstItem() + $key }}</td>
              <td>{{ $m->tahun_pelajaran }}</td>
              <td class="text-center"><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($tp[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
            </tr>
          @endforeach
        @else
            <tr>
                <td colspan="3">{{ __('Belum ada data') }}</td>
            </tr>
        @endif
        </tfoot>
      </table>
    </div>
  </div>
</div>
<div class="float-right">
  {{ $tp->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Tambah Tahun Pelajaran</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.tp.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
              <label for="tahun" class="col-sm-4 col-form-label">Tahun Pelajaran</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="tahun" name="tahun" placeholder="Contoh: 2024/2025">
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
          <h4 class="blue bigger">Hapus Tahun Pelajaran</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.tp.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus tahun pelajaran <b><span id="delete_name" class="bolder"></span></b>?</p>
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
    $("#button-save").text("Tambah Tahun Pelajaran");
    resetForm();
  }

  function deleteData(data) {
    $("#delete_id").val(data.tahun_id);
    $("#delete_name").text(data.tahun_pelajaran);
  }

  function view(url){
      window.open(url, "_blank");
  }
</script>
@endsection