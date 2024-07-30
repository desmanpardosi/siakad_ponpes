<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style type="text/css">
            body {font-family:arial;background-color :transparent;}
            .header {width:100%;border-bottom:5px solid #000;padding:2px}
            h1 {font-size: 18px;font-weight: 900;margin: 5px;text-align:center;}
            h2 {font-size: 14px;font-weight: 700;margin: 5px;text-decoration:underline;}
            h3 {font-size: 10px;margin: 0px;}
            .title {margin-top:5px;margin-bottom:0px;font-size:16px;font-weight:900;text-decoration:underline;}
            .bold {font-weight: 900;}
            small, ol > li {font-size:11px;}
            .text-center {text-align:center;vertical-align:middle;}
            .text-right {text-align:right;}
            #main {width:100%;}
            .right {position: fixed;right:0;}
            .no-padding {padding:0;}
            .no-margin {margin:0;}
            .title {margin: 10px 0 0 0;font-size:12px;}
            #footer {position: fixed;bottom: 0;width: 100%;}
            #footer > ol {margin-top:0px;padding-left: 12px;}
            #footer table {width:100%;}
            span {display: block;}
        </style>
        <title>{{ $title }}</title>
    </head>
    <body>
        <div id="main">
@if($eform == 0)
            <h1>FORM APPROVAL</h1>
            <h1>PERMINTAAN {{ $data->req_type == 0 ? "PENAMBAHAN" : ($data->req_type == 1 ? "PERUBAHAN" : "PENGHAPUSAN") }} AKUN HIS #{{ $data->req_id }}</h1>
            <table style="margin-top:50px;">
                <tr>
                    <td class="bold">User Group</td>
                    <td>:</td>
                    <td>{{ $data->nama_group }}</td>
                </tr>
                <tr>
                    <td class="bold">Nama Lengkap</td>
                    <td>:</td>
                    <td>{{ $data->fullname }}</td>
                </tr>
                <tr>
                    <td class="bold">Nama Panggilan</td>
                    <td>:</td>
                    <td>{{ $data->nickname }}</td>
                </tr>
                <tr>
                    <td class="bold">Jenis Kelamin</td>
                    <td>:</td>
                    <td>{{ $data->gender == "L"? "Laki-Laki":"Perempuan" }}</td>
                </tr>
                <tr>
                    <td class="bold">Tempat / Tanggal Lahir</td>
                    <td>:</td>
                    <td>{{ ucwords($data->birth_place) }}, {{ date("d/m/Y", strtotime($data->birth_date)) }}</td>
                </tr>
                <tr>
                    <td class="bold">No. Kartu Identitas</td>
                    <td>:</td>
                    <td>{{ $data->idcard_number }} ({{ $data->idcard_type == "0"? "KTP":"SIM" }})</td>
                </tr>
                <tr>
                    <td class="bold">Alamat</td>
                    <td>:</td>
                    <td>{{ $data->address }}</td>
                </tr>
                <tr>
                    <td class="bold">No. HP.</td>
                    <td>:</td>
                    <td>{{ $data->phone }}</td>
                </tr>
                <tr>
                    <td class="bold">E-mail</td>
                    <td>:</td>
                    <td>{{ $data->email }}</td>
                </tr>
            </table>
            <div id="footer">
                <p class="text-right">Pangkalpinang, {{ date("d/m/Y", strtotime($data->verified_3_datetime)) }}</p>
                <table>
                    <tr>
                        <td class="text-center"><img src="{{ url('/assets/images/approved.png') }}" width="82px" height="82px"></td>
                        <td class="text-center"><img src="{{ url('/assets/images/approved.png') }}" width="82px" height="82px"></td>
                        <td class="text-center"><img src="{{ url('/assets/images/approved.png') }}" width="82px" height="82px"></td>
                    </tr>
                    <tr>
                        <td class="text-center bold">({{ $data->email1_name }})</td>
                        <td class="text-center bold">({{ $data->email2_name != null? $data->email2_name:$data->email3_name }})</td>
                        <td class="text-center bold">(Koordinator IT)</td>
                    </tr>
                </table>
            </div>
@else
            <h1>FORM APPROVAL</h1>
            <h1>PERMINTAAN PERBAIKAN DATA #{{ $data->req_id }}</h1>
            <table style="margin-top:50px;">
                <tr>
                    <td class="bold">Unit</td>
                    <td>:</td>
                    <td>{{ $data->nama_group }}</td>
                </tr>
                <tr>
                    <td class="bold">Nama Lengkap</td>
                    <td>:</td>
                    <td>{{ $data->fullname }}</td>
                </tr>
                <tr>
                    <td class="bold">Tanggal Kejadian</td>
                    <td>:</td>
                    <td>{{ date("d/m/Y", strtotime($data->tgl_kejadian)) }}</td>
                </tr>
            </table>
            <h2>TELAH MELAKUKAN KESALAHAN DALAM PENGINPUTAN DATA</h2>
            <p>
                <span class="bold">Pada Module:<br></span>
                <span>{{ $data->module }}</span>
            </p>
            <p>
                <span class="bold">Kronologis:</span>
                <span>{!! Str::markdown($data->kronologis) !!}</span>
            </p>
            @if(!empty($data->note_1))
            <p>
                <span class="bold">Catatan Tambahan (Oleh {{ $data->email1_name }}):</span>
                <span>{!! Str::markdown($data->note_1) !!}</span>
            </p>
            @endif
            @if(!empty($data->note_2))
            <p>
                <span class="bold">Catatan Tambahan (Oleh {{ $data->email2_name }}):</span>
                <span>{!! Str::markdown($data->note_2) !!}</span>
            </p>
            @endif
            <div id="footer">
                <p class="text-right">Pangkalpinang, {{ date("d/m/Y", strtotime($data->verified_4_datetime)) }}</p>
                <table>
                    <tr>
                        <td class="text-center"><img src="{{ url('/assets/images/approved.png') }}" width="82px" height="82px"></td>
                        <td class="text-center"><img src="{{ url('/assets/images/approved.png') }}" width="82px" height="82px"></td>
                        <td class="text-center"><img src="{{ url('/assets/images/approved.png') }}" width="82px" height="82px"></td>
                        <td class="text-center"><img src="{{ url('/assets/images/approved.png') }}" width="82px" height="82px"></td>
                    </tr>
                    <tr>
                        <td class="text-center bold">({{ $data->email1_name }})</td>
                        <td class="text-center bold">({{ $data->email2_name }})</td>
                        <td class="text-center bold">(KADIV Keuangan)</td>
                        <td class="text-center bold">(Koordinator IT)</td>
                    </tr>
                </table>
            </div>
@endif
        </div>
    </body>
</html>