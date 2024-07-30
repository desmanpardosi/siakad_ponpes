@extends('layouts.main')
@section('title', __('Master Users'))
@section('custom-css')

@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah User</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('Username') }}</th>
            <th>{{ __('Nama') }}</th>
            <th>{{ __('Role') }}</th>
            <th>{{ __('Aksi') }}</th>
          </tr>
        </thead>
        <tbody>
          @if(count($users) > 0)
          @foreach($users as $key => $m)
          <tr>
            <td class="text-center">{{ $users->firstItem() + $key }}</td>
            <td>{{ $m->username }}</td>
            <td>{{ $m->name }}</td>
            <td>{{ $m->role_name }}</td>
            <td class="text-center"><button title="Edit" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#tambah-data" onclick="editData({{ json_encode($users[$loop->iteration-1]) }})"><i class="fa fa-edit"></i></button> @if($m->role != 0)<button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($users[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button>@endif</td>
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
  {{ $users->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="modal-title" class="blue bigger">Tambah User</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="save" action="{{ route('master.users.save') }}" method="post">
          @csrf
          <input type="hidden" id="id_user" name="id_user">
          <div class="form-group row">
            <label for="username" class="col-sm-4 col-form-label">Username</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="username" name="username">
            </div>
          </div>
          <div class="form-group row">
            <label for="name" class="col-sm-4 col-form-label">Nama</label>
            <div class="col-sm-8">
              <input type="text" class="form-control" id="name" name="name">
            </div>
          </div>
          <div class="form-group row">
            <label for="password" class="col-sm-4 col-form-label">Passsword</label>
            <div class="col-sm-8">
              <input type="password" class="form-control" id="password" name="password">
            </div>
          </div>
          <div class="form-group row">
                <label for="role" class="col-sm-4 col-form-label">Role</label>
                <div class="col-sm-8">
                  <select class="form-control select2" style="width: 100%;" id="role" name="role">
                  </select>
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
        <h4 class="blue bigger">Hapus User</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="delete" action="{{ route('master.users.delete') }}" method="post">
          @csrf
          @method('delete')
          <input type="hidden" id="delete_id" name="delete_id">
        </form>
        <div>
          <p>Anda yakin ingin menghapus user <b><span id="delete_name" class="bolder"></span></b>?</p>
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
    $("#modal-title").text("Tambah User");
    $("#button-save").text("Tambahkan");
    $("#username").prop('disabled', false);
    $("#password").prop("placeholder","");
    resetForm();
    getRoles();
  }

  function editData(data) {
    $("#button-save").show();
    $("#modal-title").text("Ubah User");
    $("#button-save").text("Simpan");
    resetForm();
    $("#id_user").val(data.id);
    $("#username").val(data.username);
    $("#username").prop('disabled', true);
    $("#password").prop("placeholder","Kosongkan jika tidak ingin diganti");
    $("#name").val(data.name);
    getRoles(data.role);
  }

  function deleteData(data) {
    $("#delete_id").val(data.id);
    $("#delete_name").text(data.username);
  }

  function getRoles(val){
      $.ajax({
          url: "{{ route('master.roles') }}",
          type: "GET",
          data: {"format": "json"},
          dataType: "json",
          success:function(data) {                    
              $('#role').empty();
              $('#role').append('<option value="">.:: Pilih Role ::.</option>');
              $.each(data, function(key, value) {
                  if(value.role_id == val){
                      $('#role').append('<option value="'+ value.role_id +'" selected>'+ value.name +'</option>');
                  } else {
                      
                      $('#role').append('<option value="'+ value.role_id +'">'+ value.name +'</option>');
                  }
              });
          }
      });
  }
</script>
@endsection