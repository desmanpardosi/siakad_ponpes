<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
    body {font-family:arial;background-color :#fff;}
    .title {font-size:16px;font-weight:900;text-decoration:underline;text-transform:uppercase;}
    small {font-size:11px;}
    table {width:100%;}
    .link {font-weight:900;padding:10px;font-size:24px;}
    .center {text-align: center;}
    </style>
  </head>
  <body>
    <h1 class="title">PERMINTAAN {{ $data["req_type"] }} AKUN HOSPITAL INFORMATION SYSTEM (HIS)</h1>
    <table>
      <tr>
        <td>User Group</td>
        <td>:</td>
        <td>{{ $data["nama_group"] }}</td>
      </tr>
      <tr>
        <td>Nama Lengkap</td>
        <td>:</td>
        <td>{{ $data["fullname"] }}</td>
      </tr>
      @if(!empty($data["idcard_type"]) && !empty($data["idcard_number"]))
      <tr>
        <td>No. Kartu Identitas</td>
        <td>:</td>
        <td>{{ $data["idcard_number"] }} ({{ $data["idcard_type"] }})</td>
      </tr>
      @endif
      @if($data['approver'] != 3)
      <tr>
        <td colspan="3" class="link center"><a href="{{ $data['link'] }}" target="_blank">Setujui</a> | <a href="{{ $data['link2'] }}" target="_blank">Tolak</a></td>
      </tr>
      @else
      <tr>
        <td colspan="3" class="link center"><a href="{{ $data['link'] }}" target="_blank">Setujui</a></td>
      </tr>
      @endif
    </table>
  </body>
</html>