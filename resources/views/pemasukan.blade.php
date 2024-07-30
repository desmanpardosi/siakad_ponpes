@extends('layouts.main')
@section('title', __('Pemasukan'))
@section('custom-css')
  <link rel="stylesheet" href="{{ url('/assets/css/datepicker.min.css') }}" />
	<link rel="stylesheet" href="{{ url('/plugins/daterangepicker/daterangepicker.css') }}">
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Pemasukan</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main">
      <div style="margin: 20px 0px;">
          <strong>Tanggal:</strong>
          <input type="text" name="daterange" value=""/>
          <button class="btn btn-success filter">Filter</button>
      </div>
      <table id="table" class="table table-responsive table-sm table-bordered table-hover dataTable dtr-inline collapsed">
        <thead>
          <tr>
            <th>ID Pemasukan</th>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Jumlah (Rp)</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="modal-title" class="blue bigger">Tambah Pemasukan</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="save" action="{{ route('pemasukan.save') }}" method="post" enctype="multipart/form-data">
          @csrf
            <div class="form-group row">
              <label for="tanggal" class="col-sm-4 col-form-label">Tanggal</label>
              <div class="col-sm-8">
                <input class="form-control date-picker" id="tanggal" name="tanggal"  type="text" data-date-format="yyyy-mm-dd" autocomplete="off"/>
              </div>
            </div>
            <div class="form-group row">
              <label for="kategori" class="col-sm-4 col-form-label">Kategori</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="kategori" name="kategori"></select>
              </div>
            </div>
            <div class="form-group row">
              <label for="jumlah" class="col-sm-4 col-form-label">Jumlah</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="jumlah" name="jumlah">
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
@endsection
@section('custom-js')
<script src="{{ url('/assets/js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ url('/plugins/moment/moment.min.js') }}"></script>
<script src="{{ url('/plugins/inputmask/min/jquery.inputmask.bundle.min.js') }}"></script>
<script src="{{ url('/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ url('/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ url('/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<script src="{{ url('/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<script src="{{ url('/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ url('/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ url('/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ url('/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ url('/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ url('/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ url('/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script>

$(function () {
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    }); 
    
    $('input[name="daterange"]').daterangepicker({
        startDate: moment().startOf('month'),
        endDate: moment()
    });

    var table = $('#table').DataTable({
        bAutoWidth: false,
        oLanguage: {
            sEmptyTable: "Belum ada data"
        },
        aoColumns : [
            { sWidth: '5%' },
            { sWidth: '25%' },
            { sWidth: '35%' },
            { sWidth: '35%' },
        ],
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            'csvHtml5','excelHtml5'
        ],
        processing: false,
        serverSide: false,
        ajax: {
            "url": "{{ route('pemasukan') }}",
            "type": "get",
            data:function (d) {
                d.fd = $('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.td = $('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        order: [[0, 'desc']],
        columns: [
            {data: 'pemasukan_id', name: 'pemasukan_id'},
            {data: 'tanggal', name: 'tanggal'},
            {data: 'kategori', name: 'kategori'},
            {data: 'jumlah', name: 'jumlah'},
        ],
        'columnDefs': [
            {"targets": [3], "className": "text-right"}
        ],
    });
    $(".filter").click(function(){
        table.ajax.reload();
    });
  });

  $('.date-picker').datepicker({
    autoclose: true,
    todayHighlight: true
  })

  function resetForm(){
    $('#save').trigger("reset");
  }

  function addData(){
    $("#modal-title").text("Tambah Pemasukan");
    $("#button-save").text("Tambahkan");
    $("#button-save").show();
    resetForm();
    getCategory();
  }

  function getCategory(val){
      $.ajax({
          url: "{{ route('master.pemasukan.kategori') }}",
          type: "GET",
          data: {"format": "json"},
          dataType: "json",
          success:function(data) {
            $('#kategori').empty();
            $('#kategori').append('<option value="">.:: Pilih Kategori ::.</option>');
            $.each(data, function(key, value) {
              $('#kategori').append('<option value="'+ value.kategori_id +'">'+ value.kategori +'</option>');
            });
          }
      });
  }
</script>
@endsection