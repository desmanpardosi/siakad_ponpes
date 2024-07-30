@extends('layouts.main')
@section('title', "Permintaan Akun")
@section('custom-css')
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th>No</th>
            <th>Tgl. Permintaan</th>
            <th>No. Kartu Identitas</th>
            <th>Nama</th>
            <th>User Group</th>
            <th>Jenis Permintaan</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        @if(!empty($permintaan))
          @foreach($permintaan as $key => $m)
            <tr>
              <td class="text-center">{{ $permintaan->firstItem() + $key }}</td>
              <td>{{ date('d/m/Y', strtotime($m->req_date)) }}</td>
              <td>{{ $m->idcard_type == 0 ? "KTP":"SIM" }} - {{ $m->idcard_number }}</td>
              <td>{{ $m->fullname }}</td>
              <td>{{ $m->nama_group }}</td>
              <td>
                @if($m->req_type == 0)
                Penambahan 
                @elseif($m->req_type == 1)
                Perubahan 
                @else
                Penghapusan 
                @endif
                Akun
              </td>
              <td class="text-center">
                @if($m->status == 0)
                <i class="fa fa-refresh" title="Diproses"></i> 
                @elseif($m->status == 1)
                <i class="fa fa-check-circle" title="Diterima"></i> 
                @else
                <i class="fa fa-times-circle" title="Ditolak"></i>  
                @endif
              </td>
              <td class="text-center"><button title="Detail" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#view-data" onclick="view('{{ route("acc_req") }}/{{ $m->req_id }}')"><i class="fa fa-eye"></i></button></td>
            </tr>
          @endforeach
        @else
            <tr>
                <td colspan="8">{{ __('Belum ada data') }}</td>
            </tr>
        @endif
        </tfoot>
      </table>
    </div>
  </div>
</div>
<div class="float-right">
  {{ $permintaan->links("pagination::bootstrap-4") }}
</div>
@endsection
@section('custom-js')
<script>
  function view(url){
      window.open(url, "_blank");
  }
</script>
@endsection