@extends('layouts.main')
@section('title', __('Transkrip Nilai'))
@section('custom-css')
@endsection
@section('content')
<div class="row">
  <div class="col-md-5">
    <div class="col-sm-5">
      Nama
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $santri->nama_lengkap }}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-5">
    <div class="col-sm-5">
      Nomor Induk Siswa (NIS)
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $santri->nis }}
    </div>
  </div>
</div>
<div class="row">
  <div class="col-md-5">
    <div class="col-sm-5">
      Tempat dan Tanggal Lahir
    </div>
    <div class="col-sm-1">
      :
    </div>
    <div class="col-sm-6">
      {{ $santri->tempat_lahir }}, {{ date("d/m/Y", strtotime($santri->tgl_lahir)) }}
    </div>
  </div>
</div>
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-title no-border">
      <select class="form-control select2" style="width: 20%;" id="tp" name="tp" onchange="ubahTahun()"></select>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Mata Pelajaran</th>
            <th colspan="2">Nilai</th>
          </tr>
          <tr>
            <th>Angka</th>
            <th>Huruf</th>
          </tr>
        </thead>
        <tbody>
        @if(count($nilai) > 0)
          @php $no = 1; @endphp
          @foreach($nilai as $key => $m)
            <tr>
              <td class="text-center">{{ $no }}</td>
              <td>{{ $m->mapel }}</td>
              <td>{{ $m->nilai }}</td>
              <td>{{ $m->nilai_huruf }}</td>
            </tr>
            @php $no++; @endphp
          @endforeach
        @else
            <tr>
                <td colspan="4">{{ __('Belum ada data') }}</td>
            </tr>
        @endif
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
@section('custom-js')
<script src="/assets/js/bootstrap-datepicker.min.js"></script>
<script>
  $(document).ready(function (){
    getTahun("{{ request()->tahun }}");
  });

  $('.date-picker').datepicker({
    autoclose: true,
    todayHighlight: true
  })

  function view(url){
      window.open(url, "_self");
  }

  function ubahTahun(){
      window.open("?tahun="+$('#tp').val(), "_self");
      getTahun("{{ request()->tahun }}");
  }

  function getTahun(val){
      $.ajax({
          url: "{{ route('master.tp') }}",
          type: "GET",
          data: {"format": "json"},
          dataType: "json",
          success:function(data) {
            $('#tp').empty();
            $.each(data, function(key, value) {
              if(value.tahun_id == val){
                $('#tp').append('<option value="'+ value.tahun_id +'" selected>'+ value.tahun_pelajaran +'</option>');
              } else {
                $('#tp').append('<option value="'+ value.tahun_id +'">'+ value.tahun_pelajaran +'</option>');
              }
            });
          }
      });
  }

</script>
@endsection