@extends('layouts.main')
@section('title', __('Master Staff'))
@section('custom-css')
  <link rel="stylesheet" href="/assets/css/datepicker.min.css" />
@endsection
@section('content')
<div class="widget-box">
  <div class="widget-header">
    <div class="widget-toolbar no-border">
      <button type="button" class="btn btn-xs btn-primary" data-toggle="modal" data-target="#tambah-data" onclick="addData()"><i class="fa fa-plus"></i> Tambah Guru</button>
    </div>
  </div>
  <div class="widget-body">
    <div class="widget-main no-padding">
      <table id="table" class="table table-striped table-bordered table-hover table-responsive" width="100%">
        <thead>
          <tr>
            <th>No.</th>
            <th>Nama Lengkap</th>
            <th>NIK</th>
            <th>Tempat / Tgl. Lahir</th>
            <th>Alamat</th>
            <th>No. HP.</th>
            <th>Pendidikan Terakhir</th>
            <th>Bidang Mengajar</th>
            <th>No. SK.</th>
            <th>Mulai Mengajar</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @if(count($staff) > 0)
          @foreach($staff as $key => $m)
            <tr>
              <td class="text-center">{{ $staff->firstItem() + $key }}</td>
              <td style="min-width: 100px;">{{ $m->nama_lengkap }}</td>
              <td>{{ $m->nik }}</td>
              <td>{{ $m->tempat_lahir }}, {{ date("d/m/Y", strtotime($m->tgl_lahir)) }}</td>
              <td>{{ $m->alamat }}</td>
              <td>{{ $m->no_hp }}</td>
              <td>{{ $m->pendidikan_terakhir }}</td>
              <td>{{ $m->bidang_mengajar }}</td>
              <td>{{ $m->no_sk }}</td>
              <td>{{ $m->mulai_mengajar }}</td>
              <td>
                @if($m->status == 0)
                Sertifikasi
                @elseif($m->status == 1)
                Honorer
                @else
                Lainnya
                @endif
              </td>
              <td class="text-center" style="min-width: 100px;"><button title="Edit" type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#tambah-data" onclick="editData({{ json_encode($staff[$loop->iteration-1]) }})"><i class="fa fa-edit"></i></button> <button title="Hapus" type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#del-data" onclick="deleteData({{ json_encode($staff[$loop->iteration-1]) }})"><i class="fa fa-trash"></i></button></td>
            </tr>
          @endforeach
        @else
            <tr>
                <td colspan="12">{{ __('Belum ada data') }}</td>
            </tr>
        @endif
        </tfoot>
      </table>
    </div>
  </div>
</div>
<div class="float-right">
  {{ $staff->links("pagination::bootstrap-4") }}
</div>
<div class="modal fade" id="tambah-data">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 id="modal-title" class="blue bigger">Tambah Staff</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="save" action="{{ route('master.staff.save') }}" method="post" enctype="multipart/form-data">
          @csrf
            <input type="hidden" id="staff_id" name="staff_id">
            <input type="hidden" id="staff_type" name="staff_type" value="1">
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
              <label for="pendidikan_terakhir" class="col-sm-4 col-form-label">Pendidikan Terakhir</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir">
              </div>
            </div>
            <div class="form-group row">
              <label for="bidang_mengajar" class="col-sm-4 col-form-label">Bidang Mengajar</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="bidang_mengajar" name="bidang_mengajar">
              </div>
            </div>
            <div class="form-group row">
              <label for="no_sk" class="col-sm-4 col-form-label">No. SK.</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="no_sk" name="no_sk">
              </div>
            </div>
            <div class="form-group row">
              <label for="mulai_mengajar" class="col-sm-4 col-form-label">Tahun Mulai Mengajar</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="mulai_mengajar" name="mulai_mengajar">
              </div>
            </div>
            <div class="form-group row">
              <label for="status" class="col-sm-4 col-form-label">Status</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="status" name="status">
                  <option value="">.:: Pilih Status ::.</option>
                  <option value="0">Sertifikasi</option>
                  <option value="1">Honorer</option>
                  <option value="2">Lainnya</option>
                </select>
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
          <h4 class="blue bigger">Hapus Staff</h4>
        </div>
          <div class="modal-body">
              <form role="form" id="delete" action="{{ route('master.staff.delete') }}" method="post">
                  @csrf
                  @method('delete')
                  <input type="hidden" id="delete_id" name="delete_id">
              </form>
              <div>
                  <p>Anda yakin ingin menghapus <b><span id="delete_name" class="bolder"></span></b> dari daftar staff?</p>
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
    $("#modal-title").text("Tambah Staff");
    $("#button-save").text("Tambahkan");
    $("#button-save").show();
    resetForm();
  }

  function editData(data) {
    $("#modal-title").text("Ubah Data Staff");
    $("#button-save").text("Simpan");
    $("#button-save").show();
    resetForm();
    $("#staff_id").val(data.staff_id);
    $("#nama_lengkap").val(data.nama_lengkap);
    $("#nik").val(data.nik);
    $("#tempat_lahir").val(data.tempat_lahir);
    $("#tgl_lahir").val(data.tgl_lahir);
    $("#alamat").val(data.alamat);
    $("#no_hp").val(data.no_hp);
    $("#pendidikan_terakhir").val(data.pendidikan_terakhir);
    $("#bidang_mengajar").val(data.bidang_mengajar);
    $("#no_sk").val(data.no_sk);
    $("#mulai_mengajar").val(data.mulai_mengajar);
    $("#status").val(data.status);
  }

  function deleteData(data) {
    $("#delete_id").val(data.staff_id);
    $("#delete_name").text(data.nama_lengkap);
  }

  function view(url){
      window.open(url, "_blank");
  }
</script>
@endsection