@extends('layouts.main')
@section('title', __('Master Kategori Pemasukan'))
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Kategori</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Kategori') }}</th>
            <th>{{ __('Tgl. Buat') }}</th>
            <th>{{ __('User') }}</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($kategori) > 0)
          @foreach($kategori as $key => $m)
            <tr>
              <td class="text-center">{{ $kategori->firstItem() + $key }}</td>
              <td>{{ $m->kategori }}</td>
              <td>{{ date("d/m/Y H:i:s", strtotime($m->tgl_buat)) }}</td>
              <td>{{ $m->user_buat }}</td>
              <td class="text-center"><button title="Edit" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#tambah-data" onclick="editData({{ json_encode($kategori[$loop->iteration-1]) }})"><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($kategori[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
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
  {{ $kategori->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="modal-title" class="blue bigger">Tambah Kategori</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.pemasukan.kategori.save') }}" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="kategori_id" name="kategori_id">
              <div class="form-group row">
                <label for="kategori" class="col-sm-4 col-form-label">Kategori</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="kategori" name="kategori">
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
          <h4 class="blue bigger">Hapus Kategori</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.pemasukan.kategori.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus <b><span id="delete_name" class="bolder"></span></b> dari daftar kategori pemasukan?</p>
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
    $("#modal-title").text("Tambah Kategori Pemasukan");
    $("#button-save").text("Tambahkan");
    $("#button-save").show();
    resetForm();
  }

  function editData(data) {
    $("#modal-title").text("Ubah Kategori");
    $("#button-save").text("Simpan");
    $("#button-save").show();
    resetForm();
    $("#kategori_id").val(data.kategori_id);
    $("#kategori").val(data.kategori);
  }

  function deleteData(data) {
    $("#delete_id").val(data.kategori_id);
    $("#delete_name").text(data.kategori);
  }

  function view(url){
      window.open(url, "_blank");
  }
</script>
@endsection