@extends('layouts.main')
@section('title', __('Laporan Keuangan'))
@section('custom-css')
  <link rel="stylesheet" href="{{ url('/assets/css/datepicker.min.css') }}" />
	<link rel="stylesheet" href="{{ url('/plugins/daterangepicker/daterangepicker.css') }}">
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#download-data"><i class="fa fa-download"></i> Download Laporan</button>
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
            <th>No.</th>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Pemasukan (Rp)</th>
            <th>Pengeluaran (Rp)</th>
          </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th colspan="3" style="text-align:center"></th>
                <th style="text-align:right"></th>
                <th style="text-align:right"></th>
            </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
<div class="modal fade" id="download-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Download Laporan</h4>
      </div>
      <div class="modal-body">
          <form role="form" id="download" action="{{ route('laporan.keuangan.download') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group row">
              <label for="jenis_lap" class="col-sm-4 col-form-label">Jenis Laporan</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="jenis_lap" name="jenis_lap" onchange="changeGroup()">
                    <option>.:: Pilih Jenis Laporan ::.</option>
                    @if(Auth::user()->role == 0 || Auth::user()->role == 1)
                    <option value="0">Laporan Harian</option>
                    @endif
                    <option value="1">Laporan Bulanan</option>
                    @if(Auth::user()->role == 0 || Auth::user()->role == 1)
                    <option value="2">Laporan Tahunan</option>
                    @endif
                </select>
              </div>
            </div>
            <div id="lap_harian" class="row" style="display:none;">
              <div class="col-sm-4"></div>
              <div class="col-sm-8">
                <div class="input-group">
                  <input class="form-control date-picker" id="lap_harian_start" name="lap_harian_start"  type="text" data-date-format="yyyy-mm-dd" autocomplete="off"/>
                  <span class="input-group-addon">s/d</span>
                  <input class="form-control date-picker" id="lap_harian_end" name="lap_harian_end"  type="text" data-date-format="yyyy-mm-dd" autocomplete="off"/>
                </div>
              </div>
            </div>
            <div id="lap_bulanan" class="row" style="display:none;">
              <div class="col-sm-4"></div>
              <div class="col-sm-8">
                <div class="col-sm-6">
                  <select class="form-control" style="width: 100%;" id="lap_bulanan_bulan" name="lap_bulanan_bulan">
                    <option>.:: Pilih Bulan ::.</option>
                    <option value="1">Januari</option>
                    <option value="2">Februari</option>
                    <option value="3">Maret</option>
                    <option value="4">April</option>
                    <option value="5">Mei</option>
                    <option value="6">Juni</option>
                    <option value="7">Juli</option>
                    <option value="8">Agustus</option>
                    <option value="9">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                  </select>
                </div>
                <div class="col-sm-6">
                  <input class="form-control" id="lap_bulanan_tahun" name="lap_bulanan_tahun" type="text" placeholder="Tahun" value="{{ date('Y') }}"/>
                </div>
              </div>
            </div>
            <div id="lap_tahunan" class="row" style="display:none;">
              <div class="col-sm-4"></div>
              <div class="col-sm-8">
                <input class="form-control" id="lap_tahunan_tahun" name="lap_tahunan_tahun" type="text" placeholder="Tahun" value="{{ date('Y') }}"/>
              </div>
            </div>
          </form>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
        <button id="button-save" type="button" class="btn btn-primary" onclick="document.getElementById('download').submit();">Download</button>
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
<script src="{{ url('/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ url('/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ url('/plugins/jszip/jszip.min.js') }}"></script>
<script>
  function changeGroup(){
    if($('#jenis_lap').val() == 0){
      $('#lap_harian').show();
      $('#lap_bulanan').hide();
      $('#lap_tahunan').hide();
    } else if($('#jenis_lap').val() == 1){
      $('#lap_harian').hide();
      $('#lap_bulanan').show();
      $('#lap_tahunan').hide();
    } else {
      $('#lap_harian').hide();
      $('#lap_bulanan').hide();
      $('#lap_tahunan').show();
    }
  }
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
            { sWidth: '20%' },
            { sWidth: '25%' },
            { sWidth: '25%' },
        ],
        dom: 'Bfrtip',
        responsive: true,
        buttons: [
            { extend: 'csvHtml5', footer: true },
            { extend: 'pdfHtml5', footer: true }
        ],
        processing: false,
        serverSide: false,
        ajax: {
            "url": "{{ route('laporan.keuangan') }}",
            "type": "get",
            data:function (d) {
                d.fd = $('input[name="daterange"]').data('daterangepicker').startDate.format('YYYY-MM-DD');
                d.td = $('input[name="daterange"]').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
        },
        order: [[0, 'asc']],
        columns: [
            {data: 'no', name: 'no'},
            {data: 'tanggal', name: 'tanggal'},
            {data: 'kategori', name: 'kategori'},
            {data: 'pemasukan', name: 'pemasukan'},
            {data: 'pengeluaran', name: 'pengeluaran'},
        ],
        'columnDefs': [
            {"targets": [3,4], "className": "text-right"}
        ],
        "footerCallback": function (row, data, start, end, display) {
            var api = this.api(), data;

            var intVal = function(i) {
                return typeof i === 'string' ?
                    i.replace(/[\$.]/g, '') * 1 :
                    typeof i === 'number' ?
                        i : 0;
            };

            var separator = function(i){
                let angka = i.toString();
                return angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            };

            var pemasukan = api
                .column(3)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            var pengeluaran = api
                .column(4)
                .data()
                .reduce(function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0);

            $(api.column(3).footer()).html(separator(pemasukan));
            $(api.column(4).footer()).html(separator(pengeluaran));
        }
    });

    $(".filter").click(function(){
        table.ajax.reload();
    });

    $('.date-picker').datepicker({
        autoclose: true,
        todayHighlight: true
    });
});
</script>
@endsection