@extends('layouts.main')
@section('title', __('Master Assets'))
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Asset</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Ruangan</th>
            <th>Nama Asset</th>
            <th>Jumlah</th>
            <th>Tgl. Buat</th>
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
        <h4 class="blue bigger">Tambah Asset</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.asset.save') }}" method="post" enctype="multipart/form-data">
              @csrf
              <input type="hidden" id="asset_id" name="asset_id">
              <div class="form-group row">
                <label for="ruangan" class="col-sm-4 col-form-label">Ruangan</label>
                <div class="col-sm-8">
                  <select class="form-control select2" style="width: 100%;" id="ruangan" name="ruangan"></select>
                </div>
              </div>
              <div class="form-group row">
                <label for="nama_asset" class="col-sm-4 col-form-label">Nama Asset</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="nama_asset" name="nama_asset">
                </div>
              </div>
              <div class="form-group row">
                <label for="jumlah" class="col-sm-4 col-form-label">Jumlah</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="jumlah" name="jumlah">
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
          <h4 class="blue bigger">Hapus Asset</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.asset.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus <b><span id="delete_name" class="bolder"></span></b> dari daftar asset?</p>
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
    getRuangan();
  }

  function deleteData(data) {
    $("#delete_id").val(data.asset_id);
    $("#delete_name").text(data.nama_asset);
  }

  function getRuangan(val){
      $.ajax({
          url: "{{ route('master.ruangan') }}",
          type: "GET",
          data: {"format": "json"},
          dataType: "json",
          success:function(data) {
            $('#ruangan').empty();
            $('#ruangan').append('<option value="">.:: Pilih Ruangan ::.</option>');
            $.each(data, function(key, value) {
              $('#ruangan').append('<option value="'+ value.ruangan_id +'">'+ value.nama_ruangan +'</option>');
            });
          }
      });
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
          aoColumns : [
              { sWidth: '5%' },
              { sWidth: '20%' },
              { sWidth: '20%' },
              { sWidth: '20%' },
              { sWidth: '25%' },
              { sWidth: '10%' },
          ],
          dom: 'Bfrtip',
          responsive: true,
          buttons: [
              { extend: 'excelHtml5'},
              {
                extend: 'pdfHtml5',
                customize: function (doc) {
                    doc.content[1].table.widths = [ '5%',  '20%', '20%', '20%', '25%', '10%'];
                }
              }
          ],
          processing: false,
          serverSide: false,
          ajax: {
              "url": "{{ route('master.assets') }}",
              "data": {"ruangan": "{{ request()->ruangan }}"},
              "type": "get"
          },
          order: [[0, 'asc']],
          columns: [
              {data: 'no', name: 'no'},
              {data: 'nama_ruangan', name: 'nama_ruangan'},
              {data: 'nama_asset', name: 'nama_asset'},
              {data: 'jumlah', name: 'jumlah'},
              {data: 'tgl_buat', name: 'tgl_buat'},
              {data: 'action', name: 'action'},
          ],
          'columnDefs': [
              {"targets": [3,4], "className": "text-right"}
          ]
      });
  });
</script>
@endsection