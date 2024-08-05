@extends('layouts.main')
@section('title', __('Nilai'))
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
            <th>No.</th>
            <th>Mata Pelajaran</th>
            <th>Kelas / Semester</th>
            <th>Tahun Pelajaran</th>
            <th>Guru</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($mapel) > 0)
          @foreach($mapel as $key => $m)
            <tr>
              <td class="text-center">{{ $mapel->firstItem() + $key }}</td>
              <td>{{ $m->mapel }}</td>
              <td>{{ $m->kelas_semester }}</td>
              <td>{{ $m->tahun_pelajaran }}</td>
              <td>{{ $m->guru }}</td>
              <td class="text-center"><button type="button" class="btn btn-success btn-xs" onclick="view('{{ route("nilai") }}/{{ $m->mapel_id }}')"><i class="fa fa-calendar-check-o"></i> Input Nilai</button></td>
            </tr>
          @endforeach
        @else
            <tr>
                <td colspan="6">{{ __('Belum ada data') }}</td>
            </tr>
        @endif
        </tfoot>
      </table>
    </div>
  </div>
</div>
<div class="float-right">
  {{ $mapel->links("pagination::bootstrap-4") }}
</div>
@endsection
@section('custom-js')
<script>
  function resetForm(){
    $('#save').trigger("reset");
  }

  function view(url){
      window.open(url, "_self");
  }
</script>
@endsection