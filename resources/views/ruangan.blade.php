@extends('layouts.main')
@section('title', __('Master Ruangan'))
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Ruangan</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Nama Ruangan</th>
            <th>Jumlah Asset</th>
            <th>Tgl. Buat</th>
            <th>User</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($ruangan) > 0)
          @foreach($ruangan as $key => $m)
            <tr>
              <td class="text-center">{{ $ruangan->firstItem() + $key }}</td>
              <td>{{ $m->nama_ruangan }}</td>
              <td class="text-center"><a href="{{ route('master.assets') }}?ruangan={{ $m->ruangan_id }}">{{ $m->jumlah_asset }}</a></td>
              <td>{{ date("d/m/Y H:i:s", strtotime($m->tgl_buat)) }}</td>
              <td>{{ $m->user_buat }}</td>
              <td class="text-center"><button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($ruangan[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
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
  {{ $ruangan->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Tambah Ruangan</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.ruangan.save') }}" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="ruangan_id" name="ruangan_id">
              <div class="form-group row">
                <label for="nama_ruangan" class="col-sm-4 col-form-label">Nama Ruangan</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="nama_ruangan" name="nama_ruangan">
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
          <h4 class="blue bigger">Hapus Ruangan</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.ruangan.delete') }}" method="post">
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
    $("#delete_id").val(data.ruangan_id);
    $("#delete_name").text(data.nama_ruangan);
  }

  function view(url){
      window.open(url, "_blank");
  }
</script>
@endsection