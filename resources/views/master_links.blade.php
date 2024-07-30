@extends('layouts.main')
@section('title', __('Master Links'))
@section('custom-css')

@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Link</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Judul') }}</th>
            <th>{{ __('Unit') }}</th>
            <th>{{ __('URL') }}</th>
            <th>{{ __('Aksi') }}</th>
          </tr>
        </thead>
        <tbody>
        @if(count($links) > 0)
          @foreach($links as $key => $m)
            <tr>
              <td class="text-center">{{ $links->firstItem() + $key }}</td>
              <td>{{ $m->title }}</td>
              <td>{{ $m->nama_group }}</td>
              <td><input type="text" class="form-control" value="{{ $m->url }}" readonly/></td>
              <td class="text-center"><button title="Edit" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#tambah-data" onclick="editData({{ json_encode($links[$loop->iteration-1]) }})"><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($links[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
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
  {{ $links->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="modal-title" class="blue bigger">Tambah Link</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.link.save') }}" method="post">
              @csrf
              <input type="hidden" id="link_id" name="link_id">
              <div class="form-group row">
                <label for="category" class="col-sm-4 col-form-label">Judul</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="title" name="title">
                </div>
              </div>
              <div class="form-group row">
                <label for="unit" class="col-sm-4 col-form-label" id="lbl_unit">Unit</label>
                <div class="col-sm-8">
                  <select class="form-control select2" style="width: 100%;" id="unit" name="unit">
                  </select>
                </div>
              </div>
              <div class="form-group row">
                <label for="category" class="col-sm-4 col-form-label">URL</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="url" name="url" placeholder="https://">
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
            <h4 class="blue bigger">Hapus Link</h4>
          </div>
            <div class="modal-body">
                <form role="form" id="delete" action="{{ route('master.link.delete') }}" method="post">
                    @csrf
                    @method('delete')
                    <input type="hidden" id="delete_id" name="delete_id">
                </form>
                <div>
                    <p>Anda yakin ingin menghapus link <b><span id="delete_name" class="bolder"></span></b>?</p>
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
    $("#modal-title").text("Tambah Link");
    $("#button-save").text("Tambahkan");
    getUserGroup();
    resetForm();    
  }

  function editData(data) {
    $("#button-save").show();
    $("#modal-title").text("Ubah Link");
    $("#button-save").text("Simpan");
    resetForm();
    $("#link_id").val(data.id);
    $("#title").val(data.title);
    $("#url").val(data.url);
    getUserGroup(data.unit);
  }

  function deleteData(data) {
    $("#delete_id").val(data.id);
    $("#delete_name").text(data.title);
  }

  function getUserGroup(val=null){
      $.ajax({
          url: "{{ route('usergroup') }}",
          type: "GET",
          dataType: "json",
          success:function(data) {
            $('#unit').empty();
            $('#unit').append('<option value="">.:: Pilih Unit ::.</option>');
            $.each(data, function(key, value) {
              if(value.group_id == val){
                $('#unit').append('<option value="'+ value.group_id +'" selected>'+ value.nama_group +'</option>');
              } else {
                $('#unit').append('<option value="'+ value.group_id +'">'+ value.nama_group +'</option>');
              }
            });
          }
      });
  }
</script>
@endsection