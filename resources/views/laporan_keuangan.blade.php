@extends('layouts.main')
@section('title', __('Laporan Keuangan'))
@section('custom-css')
  <link rel="stylesheet" href="{{ url('/assets/css/datepicker.min.css') }}" />
	<link rel="stylesheet" href="{{ url('/plugins/daterangepicker/daterangepicker.css') }}">
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
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

        },
    });
    $(".filter").click(function(){
        table.ajax.reload();
    });
  });

  $('.date-picker').datepicker({
    autoclose: true,
    todayHighlight: true
  })
</script>
@endsection