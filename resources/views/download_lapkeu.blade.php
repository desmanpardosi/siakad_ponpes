<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <style type="text/css">
            body {font-family:arial;background-color :transparent;}
            .header {width:100%;border-bottom:5px solid #000;padding:2px}
            h1 {font-size: 18px;font-weight: 900;margin: 5px;text-align:center;}
            h2 {font-size: 16px;font-weight: 700;margin: 5px;}
            h3 {font-size: 10px;margin: 0px;}
            .title {margin-top:5px;margin-bottom:0px;font-size:16px;font-weight:900;text-decoration:underline;}
            .bold {font-weight: 900;}
            small, ol > li {font-size:11px;}
            .text-left {text-align:left;}
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
            .list{list-style-type: lower-alpha;}
            table{width:100%;}
            .row {display: block;margin-top:20px;}
        </style>
        <title></title>
    </head>
    <body>
        <div id="main">
            <h1>LAPORAN LABA/RUGI</h1>
            <h1 style="text-transform: uppercase;">{{ $nama_yayasan }}</h1>
            <h1 style="text-transform: uppercase;">{{ $periode }}</h1>
            <div class="row">
                <h2>Pendapatan</h2>
                <table>
                    <tbody>
                    @php
                    $i                  = "a";
                    $total_pendapatan   = 0;
                    @endphp
                    @foreach($kategori_pemasukan as $k1)
                        <tr>
                            <td>{{ $i }}. {{ $k1->kategori }}</td>
                            <td style="width:30%;" class="text-right">Rp.</td>
                            <td style="padding-right:20px;" class="text-right">{{ number_format($k1->total, 0, ",",".") }}</td>
                        </tr>
                    @php
                    $i++;
                    $total_pendapatan += $k1->total;
                    @endphp
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="text-right">_______________ +</td>
                        </tr>
                        <tr>
                            <td>Jumlah Pendapatan</td>
                            <td></td>
                            <td style="padding-right:20px;" class="text-right">Rp. {{ number_format($total_pendapatan, 0, ",",".") }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row">
                <h2>Beban</h2>
                <table>
                    <tbody>
                    @php
                    $i              = "a";
                    $total_beban    = 0;
                    @endphp
                    @foreach($kategori_pengeluaran as $k1)
                        <tr>
                            <td>{{ $i }}. {{ $k1->kategori }}</td>
                            <td style="width:30%;" class="text-right">Rp.</td>
                            <td style="padding-right:20px;" class="text-right">{{ number_format($k1->total, 0, ",",".") }}</td>
                        </tr>
                    @php
                    $i++;
                    $total_beban += $k1->total;
                    @endphp
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="text-right">_______________ +</td>
                        </tr>
                        <tr>
                            <td>Jumlah Beban</td>
                            <td></td>
                            <td style="padding-right:20px;" class="text-right">Rp. {{ number_format($total_beban, 0, ",",".") }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row">
                <table>
                    <tbody>
                    @php
                    $i              = "a";
                    $total_sisa     = $total_pendapatan-$total_beban;
                    @endphp
                        <tr>
                            <td>Sisa Lebih/Kurang</td>
                            <td style="width:30%;" class="text-right">Pendapatan :</td>
                            <td style="padding-right:20px;" class="text-right">{{ number_format($total_pendapatan, 0, ",",".") }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="width:30%;" class="text-right">Beban :</td>
                            <td style="padding-right:20px;" class="text-right">{{ number_format($total_beban, 0, ",",".") }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td class="text-right">_______________ -</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="padding-right:20px;" class="text-right">Rp. {{ number_format($total_sisa, 0, ",",".") }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="row" style="margin-top:150px;">
                <table>
                    <tbody>
                        <tr>
                            <td style="width:30%;"></td>
                            <td style="width:30%;"></td>
                            <td class="text-left">{{ $kota }}, {{ $today }}</td>
                        </tr>
                        <tr>
                            <td style="width:30%;"></td>
                            <td style="width:30%;"></td>
                            <td class="text-left">Ketua {{ $nama_yayasan }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row" style="margin-top:60px;">
                <table>
                    <tbody>
                        <tr>
                            <td style="width:30%;"></td>
                            <td style="width:30%;"></td>
                            <td class="text-left" style="font-weight:900;text-decoration:underline;">{{ $ketua_yayasan }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>