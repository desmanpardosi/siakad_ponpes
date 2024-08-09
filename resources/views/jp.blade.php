@extends('layouts.main')
@section('title', __('Master Jam Pelajaran'))
@section('custom-css')
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Jam Pelajaran</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Hari</th>
            <th>Jam</th>
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
        <h4 class="blue bigger">Tambah Jam Pelajaran</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.jp.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
              <label for="hari" class="col-sm-4 col-form-label">Hari</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="hari" name="hari">
                  <option value="0">Senin</option>
                  <option value="1">Selasa</option>
                  <option value="2">Rabu</option>
                  <option value="3">Kamis</option>
                  <option value="4">Jum'at</option>
                  <option value="5">Sabtu</option>
                  <option value="6">Minggu</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="mapel" class="col-sm-4 col-form-label">Jam Pelajaran</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="jam" name="jam" placeholder="Contoh: 07.00 - 08.00">
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
          <h4 class="blue bigger">Hapus Jam Pelajaran</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.jp.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus jam pelajaran ini?</p>
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
    $("#button-save").text("Tambah Jam Pelajaran");
    resetForm();
  }

  function deleteData(data) {
    $("#delete_id").val(data.jp_id);
    $("#delete_name").text(data.jam);
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
          columnDefs: [{ width: '8%', targets: [0,3] }],
          responsive: true,
          buttons: [
              { extend: 'excelHtml5'},
              { extend: 'pdfHtml5', orientation: 'potrait'}
          ],
          processing: false,
          serverSide: false,
          ajax: {
              "url": "{{ route('master.jp') }}",
              "type": "get"
          },
          order: [[0, 'asc']],
          columns: [
              {data: 'no', name: 'no'},
              {data: 'nama_hari', name: 'nama_hari'},
              {data: 'jam', name: 'jam'},
              {data: 'action', name: 'action'},
          ],
      });
  });
</script>
@endsection