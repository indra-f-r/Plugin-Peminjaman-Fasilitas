<?php

define('INDEX_AUTH',1);
require '../../sysconfig.inc.php';

date_default_timezone_set('Asia/Jakarta');

global $dbs;

/* ============================
AMBIL DATA PEMINJAMAN
============================ */

$id=(int)$_GET['id'];

$query=$dbs->query("
SELECT *
FROM facility_loan
WHERE loan_id=$id
");

$data=$query->fetch_assoc();

/* ============================
GENERATE NOMOR SURAT
============================ */

$prefix = strtoupper(substr($data['borrower_name'],0,3));
$tanggal = date('Ymd',strtotime($data['start_datetime']));
$index = str_pad($data['loan_id'],3,'0',STR_PAD_LEFT);

$nomor_surat = $tanggal.'/PUS/'.$prefix.'/'.$index;

/* ============================
FORMAT LIST FASILITAS
============================ */

$fasilitas_list = '';

if(!empty($data['items'])){

$items = explode(';',$data['items']);

foreach($items as $item){

$item = trim($item);

if($item!=''){
$fasilitas_list .= "• ".$item."<br>";
}

}

}

?>
<!DOCTYPE html>
<html>
<head>
<title>Surat Izin Peminjaman</title>

<style>

body{
font-family:Arial,Helvetica,sans-serif;
width:210mm;
margin:auto;
}

.judul{
text-align:center;
font-weight:bold;
font-size:20px;
margin-top:20px;
}

.sub{
text-align:center;
margin-bottom:20px;
}

table{
border-collapse:collapse;
margin-top:20px;
}

td{
padding:4px 8px;
vertical-align:top;
}

.ttd{
margin-top:80px;
display:flex;
justify-content:space-between;
text-align:center;
}

.kembali{
margin-top:80px;
border-top:1px solid #000;
padding-top:20px;
}

@media print{
button{
display:none;
}
}

</style>

</head>

<body onload="window.print()">

<div class="judul">
SURAT IZIN PEMINJAMAN FASILITAS
</div>

<div class="sub">
Nomor : <?= $nomor_surat ?>
</div>

Yang bertanda tangan di bawah ini memberikan izin penggunaan fasilitas perpustakaan kepada:

<table>

<tr>
<td width="150">Nama</td>
<td width="10">:</td>
<td><?= $data['borrower_name'] ?></td>
</tr>

<tr>
<td>Kelas</td>
<td>:</td>
<td><?= $data['borrower_class'] ?></td>
</tr>

<tr>
<td>Kontak</td>
<td>:</td>
<td><?= $data['contact'] ?></td>
</tr>

<tr>
<td>Penanggung Jawab</td>
<td>:</td>
<td><?= $data['supervisor'] ?></td>
</tr>

<tr>
<td>Kegiatan</td>
<td>:</td>
<td><?= $data['activity_name'] ?></td>
</tr>

<tr>
<td>Lokasi</td>
<td>:</td>
<td>
<?= ucfirst($data['location_type']) ?> - <?= $data['location_name'] ?>
</td>
</tr>

<tr>
<td>Fasilitas</td>
<td>:</td>
<td><?= $fasilitas_list ?></td>
</tr>
<tr>
<td>Waktu Persetujuan</td>
<td>:</td>
<td>
<?php

if($data['approved_at']){
echo $data['approved_at'];
}
elseif($data['rejected_at']){
echo $data['rejected_at'];
}
else{
echo '-';
}

?>
</td>
</tr>

<tr>
<td>Waktu Pinjam</td>
<td>:</td>
<td><?= $data['start_datetime'] ?></td>
</tr>

<tr>
<td>Waktu Selesai</td>
<td>:</td>
<td><?= $data['end_datetime'] ?></td>
</tr>

</table>

<div class="ttd">

<div>
Peminjam
<br><br><br><br>
( ____________________ )
</div>

<div>
Petugas Perpustakaan
<br><br><br><br>
( ____________________ )
</div>

</div>


<div class="kembali">

<b>Catatan Pengembalian</b>

<br><br>

<table>

<tr>
<td width="200">Tanggal Pengembalian</td>
<td width="10">:</td>
<td>....................................</td>
</tr>

<tr>
<td>Kondisi Barang</td>
<td>:</td>
<td>

□ Baik  
<br>
□ Rusak  
<br>
□ Tidak Lengkap

</td>
</tr>

<tr>
<td>Catatan</td>
<td>:</td>
<td>......................................................................................</td>
</tr>

</table>

<br><br>

Petugas Penerima

<br><br><br>

( ____________________ )

</div>

</body>
</html>