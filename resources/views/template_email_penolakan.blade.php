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
    <h1 class="title">PENOLAKAN {{ $data["req_type"] }} AKUN HOSPITAL INFORMATION SYSTEM (HIS)</h1>
    <table>
      <tr>
        <td>Nama Lengkap</td>
        <td>:</td>
        <td>{{ $data["fullname"] }}</td>
      </tr>
      <tr>
        <td>Alasan Penolakan</td>
        <td>:</td>
        <td>{{ $data["alasan"] }}</td>
      </tr>
    </table>
  </body>
</html>