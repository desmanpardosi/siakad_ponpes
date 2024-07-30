@extends('layouts.main')
@section('title', __('Master Kategori'))
@section('custom-css')

@endsection
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
            <th>{{ __('Aksi') }}</th>
          </tr>
        </thead>
        <tbody>
        @if(count($cat) > 0)
          @foreach($cat as $key => $m)
            <tr>
              <td class="text-center">{{ $cat->firstItem() + $key }}</td>
              <td>{{ $m->category_name }}</td>
              <td class="text-center"><button title="Edit" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#tambah-data" onclick="editData({{ json_encode($cat[$loop->iteration-1]) }})"><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($cat[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
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
  {{ $cat->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Tambah Kategori</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.category.save') }}" method="post">
              @csrf
              <input type="hidden" id="id_category" name="id_category">
              <div class="form-group row">
                <label for="category" class="col-sm-4 col-form-label">Kategori</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="category" name="category">
                </div>
              </div>
          </form>
          <div id="note"></div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
        <button id="button-save" type="button" class="btn btn-primary" onclick="document.getElementById('save').submit();">{{ __('Tambahkan') }}</button>
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
                <form role="form" id="delete" action="{{ route('master.category.delete') }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" id="delete_id" name="delete_id">
                </form>
                <div>
                    <p>Anda yakin ingin menghapus kategori <b><span id="delete_name" class="bolder"></span></b>?</p>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
                <button id="button-delete" type="button" class="btn btn-danger" onclick="$('#delete').submit();">{{ __('Ya, hapus') }}</button>
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
    $("#modal-title").text("Tambah Kategori");
    $("#button-save").text("Tambahkan");
    resetForm();    
  }

  function editData(data) {
    $("#button-save").show();
    $("#modal-title").text("Ubah Kategori");
    $("#button-save").text("Simpan");
    resetForm();
    $("#id_category").val(data.id_category);
    $("#category").val(data.category_name);
  }

  function deleteData(data) {
    $("#delete_id").val(data.id_category);
    $("#delete_name").text(data.category_name);
  }
</script>
@endsection