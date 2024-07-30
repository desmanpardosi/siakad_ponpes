@extends('layouts.main')
@section('title', __('Master Sub Kategori'))
@section('custom-css')

@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Sub Kategori</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Kategori') }}</th>
            <th>{{ __('Sub Kategori') }}</th>
            <th>{{ __('Aksi') }}</th>
          </tr>
        </thead>
        <tbody>
          @if(count($cat) > 0)
          @foreach($cat as $key => $m)
          <tr>
            <td class="text-center">{{ $cat->firstItem() + $key }}</td>
            <td>{{ $m->category_name }}</td>
            <td>{{ $m->subcategory_name }}</td>
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
        <h4 class="blue bigger">Tambah Sub Kategori</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="save" action="{{ route('master.subcategory.save') }}" method="post">
          @csrf
          <input type="hidden" id="id_subcategory" name="id_subcategory">
          <div class="form-group row">
            <label for="category" class="col-sm-4 col-form-label">Kategori</label>
            <div class="col-sm-8">
              <select class="form-control select2" style="width: 100%;" id="category" name="category">
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="title" class="col-sm-4 col-form-label">Sub Kategori</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="subcategory" name="subcategory">
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
        <h4 class="blue bigger">Hapus Sub Kategori</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="delete" action="{{ route('master.subcategory.delete') }}" method="post">
          @csrf
          @method('delete')
          <input type="hidden" id="delete_id" name="delete_id">
        </form>
        <div>
          <p>Anda yakin ingin menghapus sub kategori <b><span id="delete_name" class="bolder"></span></b>?</p>
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
  function resetForm() {
    $('#save').trigger("reset");
  }

  function addData() {
    $("#button-save").show();
    $("#modal-title").text("Tambah Sub Kategori");
    $("#button-save").text("Tambahkan");
    resetForm();
    getCategory();
  }

  function editData(data) {
    $("#button-save").show();
    $("#modal-title").text("Ubah Sub Kategori");
    $("#button-save").text("Simpan");
    resetForm();
    $("#id_subcategory").val(data.id_subcategory);
    $("#subcategory").val(data.subcategory_name);
    getCategory(data.id_category);
  }

  function getCategory(val){
      $.ajax({
          url: "{{ route('master.category') }}",
          type: "GET",
          data: {"format": "json"},
          dataType: "json",
          success:function(data) {                    
              $('#category').empty();
              $('#category').append('<option value="">.:: Pilih Kategori ::.</option>');
              $.each(data, function(key, value) {
                  if(value.id_category == val){
                      $('#category').append('<option value="'+ value.id_category +'" selected>'+ value.category_name +'</option>');
                  } else {
                      
                      $('#category').append('<option value="'+ value.id_category +'">'+ value.category_name +'</option>');
                  }
              });
          }
      });
  }

  function deleteData(data) {
    $("#delete_id").val(data.id_subcategory);
    $("#delete_name").text(data.subcategory_name);
  }
</script>
@endsection