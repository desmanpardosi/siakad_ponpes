@extends('layouts.main')
@section('title', __('Links'))
@section('content')
<div class="widget-box">
  <div class="widget-header">
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>Judul</th>
            <th>Unit</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
  </div>
</div>
</section>
@endsection
@section('custom-js')
<script src="{{ url('/plugins/toastr/toastr.min.js') }}"></script>
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

<script>
  $(function () {
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });

    var table = $('#table').DataTable({
        bAutoWidth: false,
        pageLength: 10,
        aoColumns : [
            { sWidth: '50%' },
            { sWidth: '25%' },
            { sWidth: '25%' },
        ],
        oLanguage: {
            sEmptyTable: "Belum ada data"
        },
        processing: false,
        serverSide: true,
        scrollX: true,
        ajax: {
            "url": "{{ route('links') }}",
            "type": "get"
        },
        order: [[0, 'asc']],
        columns: [
            {data: 'title', name: 'title'},
            {data: 'unit', name: 'unit'},
            {data: 'url', name: 'url'},
        ]
    });
  });
    function visit(url){
        window.open(url, "_blank");
    }
  
</script>
@endsection