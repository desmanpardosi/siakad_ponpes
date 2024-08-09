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
    <div class="widget-main">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Kategori</th>
            <th>Tanggal Buat</th>
            <th>User Buat</th>
            <th></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
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

  $(function () {
      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      }); 
      
      var table = $('#table').DataTable({
          bAutoWidth: false,
          oLanguage: {
              sEmptyTable: "Belum ada data"
          },
          dom: 'Bfrtip',
          columnDefs: [{ width: '8%', targets: [0,4] }],
          responsive: true,
          buttons: [
              { extend: 'excelHtml5'},
              { extend: 'pdfHtml5', orientation: 'potrait'}
          ],
          processing: false,
          serverSide: false,
          ajax: {
              "url": "{{ route('master.pemasukan.kategori') }}",
              "type": "get"
          },
          order: [[0, 'asc']],
          columns: [
              {data: 'no', name: 'no'},
              {data: 'kategori', name: 'kategori'},
              {data: 'tgl_buat', name: 'tgl_buat'},
              {data: 'user_buat', name: 'user_buat'},
              {data: 'action', name: 'action'},
          ],
      });
  });
</script>
@endsection