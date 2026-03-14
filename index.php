<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require SB.'admin/default/session.inc.php';
date_default_timezone_set('Asia/Jakarta');
$dbs->query("SET time_zone = '+07:00'");
global $dbs,$sysconf;

/* APPROVE */
if(isset($_POST['approve'])){
$id=(int)$_POST['approve'];
$data=$dbs->query("SELECT borrower_name,start_datetime,loan_id FROM facility_loan WHERE loan_id=$id")->fetch_assoc();
$prefix=strtoupper(substr($data['borrower_name'],0,3));
$tanggal=date('Ymd',strtotime($data['start_datetime']));
$index=str_pad($data['loan_id'],3,'0',STR_PAD_LEFT);
$nomor=$tanggal.'/PUS/'.$prefix.'/'.$index;
$dbs->query("UPDATE facility_loan SET status='approved',loan_number='$nomor',approved_at=NOW() WHERE loan_id=$id");
echo "<div class='alert alert-success'>Permohonan disetujui</div>";
}

/* REJECT */
if(isset($_POST['reject'])){
$id=(int)$_POST['reject'];
$dbs->query("UPDATE facility_loan SET status='rejected',rejected_at=NOW() WHERE loan_id=$id");
echo "<div class='alert alert-warning'>Permohonan ditolak</div>";
}

/* DELETE */
if(isset($_POST['delete'])){
$id=(int)$_POST['delete'];
$dbs->query("DELETE FROM facility_loan WHERE loan_id=$id");
echo "<div class='alert alert-success'>Data berhasil dihapus</div>";
}

/* DATA */
/* FILTER TANGGAL */

$start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01');
$end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-t');

/* DATA */

$query=$dbs->query("
SELECT *
FROM facility_loan
WHERE DATE(start_datetime) BETWEEN '$start' AND '$end'
ORDER BY loan_id DESC
");

/* STATISTIK */
$stat=$dbs->query("SELECT items FROM facility_loan WHERE status='approved'");
$counter=[];
while($row=$stat->fetch_assoc()){
$items=explode(';',$row['items']);
foreach($items as $item){
$item=trim($item);
if($item!=''){
if(!isset($counter[$item])){$counter[$item]=0;}
$counter[$item]++;
}
}
}
arsort($counter);
$top=array_slice($counter,0,3,true);
?>

<style>
.badge{padding:4px 8px;border-radius:4px;color:#fff;font-size:12px}
.badge-pending{background:#f0ad4e}
.badge-approved{background:#28a745}
.badge-rejected{background:#d9534f}
.s-table th{text-align:center;vertical-align:middle}
.s-table td{text-align:left;vertical-align:top}
.s-table td{
padding-top:8px !important;
vertical-align:top !important;
}

.s-table td:nth-child(6){
vertical-align:top !important;
}
.action-grid{display:grid;grid-template-columns:1fr 1fr;gap:4px;max-width:150px}
.action-grid form,.action-grid button{margin:0}
.action-grid button{width:100%}
.dashboard{background:#f8f9fa;padding:15px;border-radius:6px;margin-bottom:20px}
@media print{
.no-print{
display:none !important;
}
body{
background:#fff;
}
.menuBoxInner{
box-shadow:none;
}
/* sembunyikan kolom aksi */
.s-table th:last-child,
.s-table td:last-child{
display:none;
}
}

.facility-item{
display:flex;
align-items:flex-start;
gap:6px;
}

.facility-bullet{
margin-top:2px;
}

</style>

<div class="menuBox">
<div class="menuBoxInner circulationIcon">

<div class="per_title"><h2>Peminjaman Fasilitas</h2></div>

<div class="no-print" style="margin-bottom:15px">

<form method="get" style="display:flex;gap:10px;align-items:center">

<input type="hidden" name="mod" value="circulation">

<label>Dari</label>
<input type="date" name="start" value="<?= $start ?>">

<label>Sampai</label>
<input type="date" name="end" value="<?= $end ?>">

<button class="btn btn-primary btn-sm">Filter</button>

<a href="?mod=circulation" class="btn btn-secondary btn-sm">Reset</a>

<button type="button" onclick="printLaporan()" class="btn btn-success btn-sm">
Print
</button>
</form>

</div>

<div class="dashboard">
<b>Fasilitas Paling Sering Dipinjam</b>
<ul style="margin-top:10px">
<?php foreach($top as $fasilitas=>$total){ ?>
<li><?= $fasilitas ?> — <?= $total ?> kali</li>
<?php } ?>
</ul>
</div>

<table class="s-table table table-striped">
<thead>
<tr>
<th>No</th>
<th>Nama</th>
<th>Kelas</th>
<th>Kegiatan</th>
<th>Lokasi</th>
<th>Fasilitas</th>
<th>Waktu Pinjam</th>
<th>Status</th>
<th>Tanggal Persetujuan</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>

<?php $no=1;while($row=$query->fetch_assoc()){ ?>
<tr>

<td><?= $no++ ?></td>
<td><?= $row['borrower_name'] ?></td>
<td><?= $row['borrower_class'] ?></td>
<td><?= $row['activity_name'] ?></td>
<td><?= ucfirst($row['location_type']) ?> - <?= $row['location_name'] ?></td>

<td style="white-space:pre-line">
<?php
$items=explode(';',$row['items']);
foreach($items as $item){
$item=trim($item);
if($item!=''){echo "<div style='margin-top:0'>• ".$item."</div>";}}
?>
</td>

<td><?= $row['start_datetime'] ?></td>

<td>
<?php
if($row['status']=='approved'){echo "<span class='badge badge-approved'>approved</span>";}
elseif($row['status']=='rejected'){echo "<span class='badge badge-rejected'>rejected</span>";}
else{echo "<span class='badge badge-pending'>pending</span>";}
?>
</td>

<td>
<?php
if($row['approved_at']){echo $row['approved_at'];}
elseif($row['rejected_at']){echo $row['rejected_at'];}
else{echo '-';}
?>
</td>

<td>
<div class="action-grid">

<?php if($row['status']=='pending'){ ?>

<form method="post" action="<?= $_SERVER['REQUEST_URI']; ?>">
<input type="hidden" name="approve" value="<?= $row['loan_id'] ?>">
<button class="btn btn-success btn-sm">Approve</button>
</form>

<form method="post" action="<?= $_SERVER['REQUEST_URI']; ?>">
<input type="hidden" name="reject" value="<?= $row['loan_id'] ?>">
<button class="btn btn-danger btn-sm">Reject</button>
</form>

<?php } else { ?>

<button class="btn btn-success btn-sm" disabled>Approve</button>
<button class="btn btn-danger btn-sm" disabled>Reject</button>

<?php } ?>

<?php if($row['status']!='pending'){ ?>

<button type="button" class="btn btn-primary btn-sm" onclick="printSurat(<?= $row['loan_id'] ?>)">Print</button>

<?php } else { ?>

<button class="btn btn-secondary btn-sm" disabled>Print</button>

<?php } ?>

<form method="post" action="<?= $_SERVER['REQUEST_URI']; ?>" onsubmit="return confirm('Hapus data ini?')">
<input type="hidden" name="delete" value="<?= $row['loan_id'] ?>">
<button class="btn btn-dark btn-sm">Hapus</button>
</form>

</div>
</td>

</tr>
<?php } ?>

</tbody>
</table>

</div>
</div>

<script>
function printSurat(id){
let petugas=prompt("Masukkan Nama Petugas:");
if(petugas==null||petugas.trim()==""){alert("Nama petugas wajib diisi");return;}

let url="../plugins/peminjaman_fasilitas/print.php?id="+id+"&petugas="+encodeURIComponent(petugas);

window.location.href=url;

}
</script>