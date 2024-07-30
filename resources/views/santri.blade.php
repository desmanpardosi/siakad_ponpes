@extends('layouts.main')
@section('title', __('Master Santri / Santri Wati'))
@section('custom-css')
  <link rel="stylesheet" href="/assets/css/datepicker.min.css" />
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Santri / Santri Wati</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <div class="table-responsive">
        <table id="table" class="table table-striped table-bordered table-hover table-responsive" width="100%">
          <thead>
            <tr>
              <th>No.</th>
              <th>NIS</th>
              <th>Nama Lengkap</th>
              <th>NIK</th>
              <th>No. KK</th>
              <th>Tempat / Tgl. Lahir</th>
              <th>Alamat</th>
              <th>No. HP.</th>
              <th>Pendidikan Formal Sekarang</th>
              <th>Kelas / Semester</th>
              <th>NISN / NIM</th>
              <th>Program Ponpes</th>
              <th>Riwayat Mondok</th>
              <th>Nama Ayah</th>
              <th>Nama Ibu</th>
              <th>No. HP. Orang Tua</th>
              <th>Alamat Orang Tua</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
          @if(count($santri) > 0)
            @foreach($santri as $key => $m)
              <tr>
                <td class="text-center">{{ $santri->firstItem() + $key }}</td>
                <td>{{ $m->nis }}</td>
                <td>{{ $m->nama_lengkap }}</td>
                <td>{{ $m->nik }}</td>
                <td>{{ $m->no_kk }}</td>
                <td>{{ $m->tempat_lahir }}, {{ date("d/m/Y", strtotime($m->tgl_lahir)) }}</td>
                <td>{{ $m->alamat }}</td>
                <td>{{ $m->no_hp }}</td>
                <td>
                  @if($m->pendidikan_formal == 0)
                  PAUD
                  @elseif($m->pendidikan_formal == 1)
                  MI
                  @elseif($m->pendidikan_formal == 2)
                  MTS
                  @else
                  SMK
                  @endif
                </td>
                <td>{{ $m->kelas_semester }}</td>
                <td>{{ $m->nisn }}</td>
                <td>
                  @if($m->program_ponpes == 0)
                  Pondok
                  @else
                  Kursus
                  @endif
                </td>
                <td>{{ $m->riwayat_mondok }}</td>
                <td>{{ $m->nama_ayah }}</td>
                <td>{{ $m->nama_ibu }}</td>
                <td>{{ $m->nohp_ortu }}</td>
                <td>{{ $m->alamat_ortu }}</td>
                <td class="text-center" style="min-width: 100px;"><button title="Edit" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#tambah-data" onclick="editData({{ json_encode($santri[$loop->iteration-1]) }})"><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($santri[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
              </tr>
            @endforeach
          @else
              <tr>
                  <td colspan="18">{{ __('Belum ada data') }}</td>
              </tr>
          @endif
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
<div class="float-right">
  {{ $santri->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="modal-title" class="blue bigger">Tambah Santri / Santri Wati</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="save" action="{{ route('master.santri.save') }}" method="post" enctype="multipart/form-data">
          @csrf
            <input type="hidden" id="santri_id" name="santri_id">
            <div class="form-group row">
              <label for="nik" class="col-sm-4 col-form-label">NIS</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="nis" name="nis">
              </div>
            </div>
            <div class="form-group row">
              <label for="nama_lengkap" class="col-sm-4 col-form-label">Nama Lengkap</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap">
              </div>
            </div>
            <div class="form-group row">
              <label for="nik" class="col-sm-4 col-form-label">NIK</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="nik" name="nik">
              </div>
            </div>
            <div class="form-group row">
              <label for="no_kk" class="col-sm-4 col-form-label">No. KK</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="no_kk" name="no_kk">
              </div>
            </div>
            <div class="form-group row">
              <label for="tempat_lahir" class="col-sm-4 col-form-label">Tempat Lahir</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir">
              </div>
            </div>
            <div class="form-group row">
              <label for="tgl_lahir" class="col-sm-4 col-form-label">Tanggal Lahir</label>
              <div class="col-sm-8">
                <input class="form-control date-picker" id="tgl_lahir" name="tgl_lahir"  type="text" data-date-format="yyyy-mm-dd" autocomplete="off"/>
              </div>
            </div>
            <div class="form-group row">
              <label for="alamat" class="col-sm-4 col-form-label">Alamat</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="alamat" name="alamat">
              </div>
            </div>
            <div class="form-group row">
              <label for="no_hp" class="col-sm-4 col-form-label">No. HP.</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="no_hp" name="no_hp">
              </div>
            </div>
            <div class="form-group row">
              <label for="pendidikan_formal" class="col-sm-4 col-form-label">Pendidikan Formal</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="pendidikan_formal" name="pendidikan_formal">
                  <option value="">.:: Pilih Pendidikan Formal ::.</option>
                  <option value="0">PAUD</option>
                  <option value="1">MI</option>
                  <option value="2">MTS</option>
                  <option value="3">SMK</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="kelas_semester" class="col-sm-4 col-form-label">Kelas / Semester</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="kelas_semester" name="kelas_semester">
              </div>
            </div>
            <div class="form-group row">
              <label for="nisn" class="col-sm-4 col-form-label">NISN / NIM</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nisn" name="nisn">
              </div>
            </div>
            <div class="form-group row">
              <label for="program_ponpes" class="col-sm-4 col-form-label">Program Ponpes</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="program_ponpes" name="program_ponpes">
                  <option value="">.:: Pilih Program Ponpes ::.</option>
                  <option value="0">Pondok</option>
                  <option value="1">Kursus</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="riwayat_mondok" class="col-sm-4 col-form-label">Riwayat Mondok</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="riwayat_mondok" name="riwayat_mondok">
              </div>
            </div>
            <div class="form-group row">
              <label for="nama_ayah" class="col-sm-4 col-form-label">Nama Ayah</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_ayah" name="nama_ayah">
              </div>
            </div>
            <div class="form-group row">
              <label for="nama_ibu" class="col-sm-4 col-form-label">Nama Ibu</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nama_ibu" name="nama_ibu">
              </div>
            </div>
            <div class="form-group row">
              <label for="nohp_ortu" class="col-sm-4 col-form-label">No. HP. Orang Tua</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nohp_ortu" name="nohp_ortu">
              </div>
            </div>
            <div class="form-group row">
              <label for="alamat_ortu" class="col-sm-4 col-form-label">Alamat Orang Tua</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="alamat_ortu" name="alamat_ortu">
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
          <h4 class="blue bigger">Hapus Santri / Santri Wati</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.santri.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus <b><span id="delete_name" class="bolder"></span></b> dari daftar santri / santri wati?</p>
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
<script src="/assets/js/bootstrap-datepicker.min.js"></script>
<script>
  $('.date-picker').datepicker({
    autoclose: true,
    todayHighlight: true
  })

  function resetForm(){
    $('#save').trigger("reset");
  }

  function addData(){
    $("#modal-title").text("Tambah Santri / Santri Wati");
    $("#button-save").text("Tambahkan");
    $("#button-save").show();
    resetForm();
  }

  function editData(data) {
    $("#modal-title").text("Ubah Data Santri / Santri Wati");
    $("#button-save").text("Simpan");
    $("#button-save").show();
    resetForm();
    $("#santri_id").val(data.santri_id);
    $("#nis").val(data.nis);
    $("#nama_lengkap").val(data.nama_lengkap);
    $("#nik").val(data.nik);
    $("#no_kk").val(data.no_kk);
    $("#tempat_lahir").val(data.tempat_lahir);
    $("#tgl_lahir").val(data.tgl_lahir);
    $("#alamat").val(data.alamat);
    $("#no_hp").val(data.no_hp);
    $("#pendidikan_formal").val(data.pendidikan_formal);
    $("#kelas_semester").val(data.kelas_semester);
    $("#nisn").val(data.nisn);
    $("#program_ponpes").val(data.program_ponpes);
    $("#riwayat_mondok").val(data.riwayat_mondok);
    $("#nama_ayah").val(data.nama_ayah);
    $("#nama_ibu").val(data.nama_ibu);
    $("#nohp_ortu").val(data.nohp_ortu);
    $("#alamat_ortu").val(data.alamat_ortu);
  }

  function deleteData(data) {
    $("#delete_id").val(data.santri_id);
    $("#delete_name").text(data.nama_lengkap);
  }

  function view(url){
      window.open(url, "_blank");
  }
</script>
@endsection