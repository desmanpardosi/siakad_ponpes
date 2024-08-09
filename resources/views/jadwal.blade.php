@extends('layouts.main')
@section('title', __('Master Jadwal Pelajaran'))
@section('custom-css')
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Jadwal Pelajaran</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No.</th>
            <th>Tahun Pelajaran</th>
            <th>Hari</th>
            <th>Jam</th>
            <th>Mata Pelajaran</th>
            <th>Kelas</th>
            <th>Guru</th>
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
        <h4 class="blue bigger">Tambah Jadwal Pelajaran</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="save" action="{{ route('master.jadwal.save') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
              <label for="mapel" class="col-sm-4 col-form-label">Mata Pelajaran</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="mapel" name="mapel"></select>
              </div>
            </div>
            <div class="form-group row">
              <label for="hari" class="col-sm-4 col-form-label">Hari</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="hari" name="hari" onchange="getJam()">
                  <option selected>.:: Pilih Hari ::.</option>
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
              <label for="jp" class="col-sm-4 col-form-label">Jam Pelajaran</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="jp" name="jp"></select>
              </div>
            </div>
            <div class="form-group row">
              <label for="tp" class="col-sm-4 col-form-label">Tahun Pelajaran</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="tp" name="tp"></select>
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
          <h4 class="blue bigger">Hapus Jadwal Pelajaran</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.jadwal.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus jadwal pelajaran ini?</p>
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
    $("#button-save").text("Tambah Jadwal Pelajaran");
    resetForm();
    getMapel();
    getTahun();
  }

  function view(url){
      window.open(url, "_blank");
  }


  function getJam(){
      $.ajax({
          url: "{{ route('master.jp') }}",
          type: "GET",
          data: {"format": "json", "hari": $("#hari").val()},
          dataType: "json",
          success:function(data) {
            $('#jp').empty();
            $('#jp').append('<option value="">.:: Pilih Jam Pelajaran ::.</option>');
            $.each(data, function(key, value) {
              $('#jp').append('<option value="'+ value.jp_id +'">'+ value.jam +'</option>');
            });
          }
      });
  }

  function getTahun(){
      $.ajax({
          url: "{{ route('master.tp') }}",
          type: "GET",
          data: {"format": "json"},
          dataType: "json",
          success:function(data) {
            $('#tp').empty();
            $('#tp').append('<option value="">.:: Pilih Tahun Pelajaran ::.</option>');
            $.each(data, function(key, value) {
              $('#tp').append('<option value="'+ value.tahun_id +'">'+ value.tahun_pelajaran +'</option>');
            });
          }
      });
  }

  function getMapel(val){
      $.ajax({
          url: "{{ route('master.mapel') }}",
          type: "GET",
          data: {"format": "json"},
          dataType: "json",
          success:function(data) {
            $('#mapel').empty();
            $('#mapel').append('<option value="">.:: Pilih Mata Pelajaran ::.</option>');
            $.each(data, function(key, value) {
              $('#mapel').append('<option value="'+ value.mapel_id +'">'+ value.mapel + ' (Kelas ' + value.kelas_semester +')</option>');
            });
          }
      });
  }

  function deleteData(data) {
    $("#delete_id").val(data.jadwal_id);
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
          columnDefs: [{ width: '8%', targets: 7 }],
          responsive: true,
          buttons: [
              { extend: 'excelHtml5'},
              { extend: 'pdfHtml5', orientation: 'potrait'}
          ],
          processing: false,
          serverSide: false,
          ajax: {
              "url": "{{ route('master.jadwal') }}",
              "type": "get"
          },
          order: [[0, 'asc']],
          columns: [
              {data: 'no', name: 'no'},
              {data: 'tahun_pelajaran', name: 'tahun_pelajaran'},
              {data: 'hari', name: 'hari'},
              {data: 'jam', name: 'jam'},
              {data: 'mapel', name: 'mapel'},
              {data: 'kelas', name: 'kelas'},
              {data: 'guru', name: 'guru'},
              {data: 'action', name: 'action'},
          ],
      });
  });
</script>
@endsection