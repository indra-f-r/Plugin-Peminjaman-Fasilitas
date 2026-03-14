<?php

define('INDEX_AUTH',1);
require '../../sysconfig.inc.php';

date_default_timezone_set('Asia/Jakarta');
setlocale(LC_TIME, 'id_ID', 'id_ID.UTF-8');

global $dbs;

/* ============================
AMBIL DATA PEMINJAMAN
============================ */

$id=(int)$_GET['id'];

$petugas = isset($_GET['petugas']) ? htmlspecialchars($_GET['petugas']) : '';

$tanggal_cetak = date('d F Y');

$query=$dbs->query("
SELECT 
facility_loan.*,
member.member_name
FROM facility_loan
LEFT JOIN member 
ON member.member_id = facility_loan.supervisor
WHERE facility_loan.loan_id=$id
");

$data=$query->fetch_assoc();

/* ambil member_id untuk QR */
$member_id = $data['supervisor'];

/*tambah fungsi tanggal*/

function formatTanggal($datetime){

$timestamp = strtotime($datetime);

$bulan = [
1=>'Januari','Februari','Maret','April','Mei','Juni',
'Juli','Agustus','September','Oktober','November','Desember'
];

$tgl = date('j',$timestamp);
$bln = $bulan[(int)date('n',$timestamp)];
$thn = date('Y',$timestamp);
$jam = date('H:i',$timestamp);

return "$tgl $bln $thn, Jam $jam";
}

$waktu_persetujuan = '-';

if($data['approved_at']){
$waktu_persetujuan = formatTanggal($data['approved_at']);
}
elseif($data['rejected_at']){
$waktu_persetujuan = formatTanggal($data['rejected_at']);
}

$waktu_pinjam = formatTanggal($data['start_datetime']);
$waktu_selesai = formatTanggal($data['end_datetime']);

$tanggal_cetak = formatTanggal(date('Y-m-d H:i:s'));


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
margin-top:60px;
display:flex;
justify-content:center;
gap:180px;
text-align:center;
}

.kembali{
margin-top:30px;
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
<td>Kelas/MGMP/Organisasi</td>
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
<td><?= $data['member_name'] ?> (<?= $data['supervisor'] ?>)</td>
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
<td><?= $waktu_persetujuan ?></td>
</tr>

<tr>
<td>Waktu Pinjam</td>
<td>:</td>
<td><?= $waktu_pinjam ?></td>
</tr>

<tr>
<td>Waktu Selesai</td>
<td>:</td>
<td><?= $waktu_selesai ?></td>
</tr>

</table>

<div class="ttd">

<div>
<br>    
Peminjam,
<br><br><br>
( <?= $data['borrower_name'] ?> )
</div>

<br>
Petugas,
<br><br><br>
( <?= $petugas ?> )
</div>

</div>

<div style="margin-top:10px;font-size:13px;">
Di cetak pada : <?= $tanggal_cetak ?>
</div>

<div style="margin-top:10px">
<img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= $member_id ?>">
</div>

<div class="kembali">

<b>Catatan Pengembalian</b>

<br><br>

<table>

<tr>
<td width="200">Tanggal Pengembalian</td>
<td width="10">:</td>
<td>___________________________________________________________</td>
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
<td>___________________________________________________________</td>
</tr>

</table>

<br><br>

Petugas Penerima

<br><br><br>

( ____________________ )

</div>

<script>
window.onafterprint=function(){
setTimeout(function(){
history.back();
},300);
};
</script>

</body>
</html>