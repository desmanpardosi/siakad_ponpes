@extends('layouts.main')
@section('title', __('e-Form'))
@section('custom-css')
  <link rel="stylesheet" href="/assets/css/datepicker.min.css" />
@endsection
@section('content')
<button type="button" class="btn btn-lg btn-primary btn-block" data-toggle="modal" data-target="#input-eform1" onclick="eform1()"><i class="fa fa-file"></i> Permintaan Akun Hospital Information System (HIS)</button>
<button type="button" class="btn btn-lg btn-primary btn-block" data-toggle="modal" data-target="#input-eform2" onclick="eform2()"><i class="fa fa-file"></i> Permintaan Perbaikan Data</button>

<div class="modal fade" id="input-eform1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Permintaan Akun Hospital Information System (HIS)</h4>
      </div>
      <div class="modal-body">
        <form id="eform1" enctype="multipart/form-data">
          @csrf
          <div class="form-group row">
            <label for="req_type" class="col-sm-4 col-form-label">Jenis Permintaan</label>
            <div class="col-sm-8">
              <select class="form-control select2" style="width: 100%;" id="req_type" name="req_type" onclick="changeGroup()">
                <option>.:: Pilih Jenis Permintaan ::.</option>
                <option value="0">Penambahan Akun</option>
                <option value="1">Perubahan Akun</option>
                <option value="2">Penghapusan Akun</option>
              </select>
            </div>
          </div>
          <div id="req_form_1">
            <div class="form-group row">
              <label for="user_group" class="col-sm-4 col-form-label" id="lbl_user_group">Unit</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="user_group" name="user_group">
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="fullname" class="col-sm-4 col-form-label">Nama Lengkap</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="fullname" name="fullname">
              </div>
            </div>
            <div class="form-group row">
              <label for="nickname" class="col-sm-4 col-form-label">Nama Panggilan</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="nickname" name="nickname">
              </div>
            </div>
            <div class="form-group row">
              <label for="gender" class="col-sm-4 col-form-label">Jenis Kelamin</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="gender" name="gender">
                  <option>.:: Pilih Jenis Kelamin ::.</option>
                  <option value="L">Laki-Laki</option>
                  <option value="P">Perempuan</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="birth_place" class="col-sm-4 col-form-label">Tempat Lahir</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="birth_place" name="birth_place">
              </div>
            </div>
            <div class="form-group row">
              <label for="birth_date" class="col-sm-4 col-form-label">Tanggal Lahir</label>
              <div class="col-sm-8">
                <input class="form-control date-picker" id="birth_date" name="birth_date"  type="text" data-date-format="yyyy/mm/dd" autocomplete="off"/>
              </div>
            </div>
            <div class="form-group row">
              <label for="idcard_type" class="col-sm-4 col-form-label">Tipe Kartu Identitas</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="idcard_type" name="idcard_type">
                  <option>.:: Pilih Tipe Identitas ::.</option>
                  <option value="0">KTP</option>
                  <option value="1">SIM</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="idcard_number" class="col-sm-4 col-form-label">No. Kartu Identitas</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="idcard_number" name="idcard_number">
              </div>
            </div>
            <div class="form-group row">
              <label for="address" class="col-sm-4 col-form-label">Alamat</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="address" name="address">
              </div>
            </div>
            <div class="form-group row">
              <label for="phone" class="col-sm-4 col-form-label">No. HP</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="phone" name="phone">
              </div>
            </div>
            <div class="form-group row">
              <label for="email" class="col-sm-4 col-form-label">E-mail</label>
              <div class="col-sm-8">
                <input type="email" class="form-control" id="email" name="email">
              </div>
            </div>
          </div>
          <div id="req_form_2" style="display:none;">
            <div class="alert alert-danger">
            <u><b>Perhatian!</b></u> Form ini hanya untuk bagian HR dan Sekretaris.
            </div>
            <div class="form-group row">
              <label for="user_group3" class="col-sm-4 col-form-label">Unit</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="user_group3" name="user_group3">
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="fullname3" class="col-sm-4 col-form-label">Nama Lengkap</label>
              <div class="col-sm-8">
                <input type="text" class="form-control" id="fullname3" name="fullname3">
              </div>
            </div>
            <div class="form-group row">
              <label for="idcard_type2" class="col-sm-4 col-form-label">Tipe Kartu Identitas</label>
              <div class="col-sm-8">
                <select class="form-control select2" style="width: 100%;" id="idcard_type2" name="idcard_type2">
                  <option>.:: Pilih Tipe Identitas ::.</option>
                  <option value="0">KTP</option>
                  <option value="1">SIM</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label for="idcard_number2" class="col-sm-4 col-form-label">No. Kartu Identitas</label>
              <div class="col-sm-8">
                <input type="number" class="form-control" id="idcard_number2" name="idcard_number2">
              </div>
            </div>
          </div>
        </form>
        <div id="note">
          <p><u>Catatan:</u></p>
          <ul>
            <li>Lengkapilah data Anda dengan sebenar-benarnya, IT tidak akan memproses permintaan Anda jika data tidak lengkap.</li>
            <li>Dilarang untuk memberitahukan password Anda kepada orang lain. Mohon untuk mengganti password Anda secara berkala.</li>
            <li>Anda bertanggungjawab sepenuhnya terhadap penyalahgunaan akun Anda.</li>
          </ul>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
        <button id="ajukan" type="button" class="btn btn-primary" onclick="submit()">Ajukan</button>
        <button id="ajukan3" style="display:none;" type="button" class="btn btn-primary" onclick="submit3()">Ajukan</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="input-eform2">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="blue bigger">Permintaan Perbaikan Data</h4>
      </div>
      <div class="modal-body">
        <form id="eform2" enctype="multipart/form-data">
          @csrf
          <div class="form-group row">
            <label for="fullname2" class="col-sm-3 col-form-label">Nama Lengkap</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="fullname2" name="fullname2">
            </div>
          </div>
          <div class="form-group row">
            <label for="user_group2" class="col-sm-3 col-form-label">Unit</label>
            <div class="col-sm-9">
              <select class="form-control select2" style="width: 100%;" id="user_group2" name="user_group2">
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="tgl_kejadian" class="col-sm-3 col-form-label">Tanggal Kejadian</label>
            <div class="col-sm-9">
              <input class="form-control date-picker" id="tgl_kejadian" name="tgl_kejadian"  type="text" data-date-format="yyyy/mm/dd" autocomplete="off"/>
            </div>
          </div>
          <div class="form-group row">
            <p class="col-sm-12" style="text-transform:uppercase;font-weight:bold;text-decoration:underline;">Telah melakukan kesalahan dalam penginputan data</p>
          </div>
          <div class="form-group row">
            <label for="module" class="col-sm-3 col-form-label">Pada Module</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="module" name="module">
            </div>
          </div>
          <div class="form-group row">
            <label for="kronologis" class="col-sm-3 col-form-label">Kronologis</label>
            <div class="col-sm-9">
              <div class="widget-box widget-color-red">
                  <div class="widget-header widget-header-small"></div>
                  <div class="widget-body">
                    <div class="widget-main no-padding">
                      <textarea id="kronologis" name="kronologis" data-provide="markdown" data-iconlibrary="fa" rows="6"></textarea>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </form>
        <p><u>Catatan:</u></p>
        <ul>
          <li>Isilah kronologis sejelas mungkin.</li>
          <li>Cantumkan No. Reg dan No. RM pasien.</li>
        </ul>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ __('Batal') }}</button>
        <button id="ajukan2" type="button" class="btn btn-primary" onclick="submit2()">Ajukan</button>
      </div>
    </div>
  </div>
</div>
@endsection
@section('custom-js')
<script src="/assets/js/jquery.ui.touch-punch.min.js"></script>
<script src="/assets/js/markdown.min.js"></script>
<script src="/assets/js/bootstrap-markdown.min.js"></script>
<script src="/assets/js/jquery.hotkeys.min.js"></script>
<script src="/assets/js/bootstrap-wysiwyg.min.js"></script>
<script src="/assets/js/jquery.maskedinput.min.js"></script>
<script src="/assets/js/bootstrap-datepicker.min.js"></script>
<script>
  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $('.date-picker').datepicker({
    autoclose: true,
    todayHighlight: true
  })

  function resetForm(){
    $('#save').trigger("reset");
  }

  function eform1(){
    getUserGroup();
  }
  
  function eform2(){
    getUserGroup(2);
  }

  function submit(){
    $('#ajukan').prop('disabled', true);
    $('#ajukan').text("Sedang Diproses...");
    $.ajax({
      async: true,
      cache: false,
      url: "{{ route('eform1.save') }}",
      type: "POST",
      dataType: "json",
      data: {
        req_type: $('#req_type').val(),
        user_group: $('#user_group').val(),
        fullname: $('#fullname').val(),
        nickname: $('#nickname').val(),
        gender: $('#gender').val(),
        birth_place: $('#birth_place').val(),
        birth_date: $('#birth_date').val(),
        idcard_type: $('#idcard_type').val(),
        idcard_number: $('#idcard_number').val(),
        address: $('#address').val(),
        phone: $('#phone').val(),
        email: $('#email').val(),
      },
      success: function (data) {
        if(data.status == 200){
          toastr.success(data.message);
          window.open(data.redirect_url, "_self");
          $('#ajukan').prop('disabled', false);
          $('#ajukan').text("Ajukan");
        } else {
          toastr.error(data.message);
          $('#ajukan').prop('disabled', false);
          $('#ajukan').text("Ajukan");
        }
      }, error: function(){
        $('#ajukan').prop('disabled', false);
        $('#ajukan').text("Ajukan");
      }
    });
  }

  function submit2(){
    $('#ajukan2').prop('disabled', true);
    $('#ajukan2').text("Sedang Diproses...");
    $.ajax({
      async: true,
      cache: false,
      url: "{{ route('eform2.save') }}",
      type: "POST",
      dataType: "json",
      data: {
        user_group: $('#user_group2').val(),
        fullname: $('#fullname2').val(),
        tgl_kejadian: $('#tgl_kejadian').val(),
        module: $('#module').val(),
        kronologis: $('#kronologis').val(),
      },
      success: function (data) {
        if(data.status == 200){
          toastr.success(data.message);
          window.open(data.redirect_url, "_self");
          $('#ajukan2').prop('disabled', false);
          $('#ajukan2').text("Ajukan");
        } else {
          toastr.error(data.message);
          $('#ajukan2').prop('disabled', false);
          $('#ajukan2').text("Ajukan");
        }
      }, error: function(){
        $('#ajukan2').prop('disabled', false);
        $('#ajukan2').text("Ajukan");
      }
    });
  }

  function submit3(){
    $('#ajukan3').prop('disabled', true);
    $('#ajukan3').text("Sedang Diproses...");
    $.ajax({
      async: true,
      cache: false,
      url: "{{ route('eform1.save') }}",
      type: "POST",
      dataType: "json",
      data: {
        req_type: $('#req_type').val(),
        user_group: $('#user_group3').val(),
        fullname: $('#fullname3').val(),
        idcard_type: $('#idcard_type2').val(),
        idcard_number: $('#idcard_number2').val(),
      },
      success: function (data) {
        if(data.status == 200){
          toastr.success(data.message);
          window.open(data.redirect_url, "_self");
          $('#ajukan3').prop('disabled', false);
          $('#ajukan3').text("Ajukan");
        } else {
          toastr.error(data.message);
          $('#ajukan3').prop('disabled', false);
          $('#ajukan3').text("Ajukan");
        }
      }, error: function(){
        $('#ajukan3').prop('disabled', false);
        $('#ajukan3').text("Ajukan");
      }
    });
  }

  function getUserGroup(i){
      $.ajax({
          url: "{{ route('usergroup') }}",
          type: "GET",
          dataType: "json",
          data: {email: i},
          success:function(data) {
            $('#user_group').empty();
            $('#user_group2').empty();
            $('#user_group3').empty();
            $('#user_group').append('<option value="">.:: Pilih Unit ::.</option>');
            $('#user_group2').append('<option value="">.:: Pilih Unit ::.</option>');
            $('#user_group3').append('<option value="">.:: Pilih Unit ::.</option>');
            $.each(data, function(key, value) {
              $('#user_group').append('<option value="'+ value.group_id +'">'+ value.nama_group +'</option>');
              $('#user_group2').append('<option value="'+ value.group_id +'">'+ value.nama_group +'</option>');
              $('#user_group3').append('<option value="'+ value.group_id +'">'+ value.nama_group +'</option>');
            });
          }
      });
  }

  function changeGroup(val){
    if($('#req_type').val() == 0){
      $('#lbl_user_group').text("Unit");
      $('#req_form_1').show();
      $('#req_form_2').hide();
      $('#note').show();
      $('#ajukan').show();
      $('#ajukan3').hide();
    }else if($('#req_type').val() == 1){
      $('#lbl_user_group').text("Unit Baru");
      $('#req_form_1').show();
      $('#req_form_2').hide();
      $('#note').show();
      $('#ajukan').show();
      $('#ajukan3').hide();
    } else {
      $('#req_form_1').hide();
      $('#req_form_2').show();
      $('#note').hide();
      $('#ajukan').hide();
      $('#ajukan3').show();
    }
  }
</script>
@endsection