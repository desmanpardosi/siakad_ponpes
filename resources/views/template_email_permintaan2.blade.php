<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style type="text/css">
    body {font-family:arial;background-color :#fff;}
    .title {font-size:16px;font-weight:900;text-decoration:underline;text-transform:uppercase;}
    .title2 {font-size:12px;font-weight:900;text-decoration:underline;text-transform:uppercase;}
    small {font-size:11px;}
    table {width:100%;}
    td {word-wrap: break-word;}
    .link {font-weight:900;padding:10px;font-size:24px;}
    .center {text-align: center;}
    </style>
  </head>
  <body>
    <h1 class="title">PERMINTAAN PERBAIKAN DATA</h1>
    <table>
      <tr>
        <td>Unit</td>
        <td>:</td>
        <td>{{ $data["nama_group"] }}</td>
      </tr>
      <tr>
        <td>Nama Lengkap</td>
        <td>:</td>
        <td>{{ $data["fullname"] }}</td>
      </tr>
      <tr>
        <td>Tanggal Kejadian</td>
        <td>:</td>
        <td>{{ date("d/m/Y", strtotime($data["tgl_kejadian"])) }}</td>
      </tr>
      <tr>
        <td class="title2" colspan="3">Telah melakukan kesalahan dalam penginputan data</td>
      </tr>
      <tr>
        <td>Pada Module</td>
        <td>:</td>
        <td>{{ $data["module"] }}</td>
      </tr>
      <tr>
        <td>Kronologis</td>
        <td>:</td>
        <td>{!! Str::markdown($data["kronologis"]) !!}</td>
      </tr>
      @if(!empty($data['note_1']))
      <tr>
        <td>Catatan Tambahan (Oleh {{ $data["email1_name"] }})</td>
        <td>:</td>
        <td>{!! Str::markdown($data["note_1"]) !!}</td>
      </tr>
      @endif
      @if(!empty($data['note_2']))
      <tr>
        <td>Catatan Tambahan (Oleh {{ $data["email2_name"] }})</td>
        <td>:</td>
        <td>{!! Str::markdown($data["note_2"]) !!}</td>
      </tr>
      @endif
      @if($data['approver'] != 4)
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