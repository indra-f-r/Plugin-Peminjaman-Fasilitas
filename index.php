<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require SB.'admin/default/session.inc.php';

date_default_timezone_set('Asia/Jakarta');
$dbs->query("SET time_zone = '+07:00'");

global $dbs,$sysconf;

/* ================================
APPROVE
================================ */

if(isset($_POST['approve'])){

$id=(int)$_POST['approve'];

$data=$dbs->query("
SELECT borrower_name,start_datetime,loan_id
FROM facility_loan
WHERE loan_id=$id
")->fetch_assoc();

$prefix=strtoupper(substr($data['borrower_name'],0,3));
$tanggal=date('Ymd',strtotime($data['start_datetime']));
$index=str_pad($data['loan_id'],3,'0',STR_PAD_LEFT);

$nomor=$tanggal.'/PUS/'.$prefix.'/'.$index;

$dbs->query("
UPDATE facility_loan
SET status='approved',
loan_number='$nomor',
approved_at=NOW()
WHERE loan_id=$id
");

echo "<div class='alert alert-success'>Permohonan disetujui</div>";

}

/* ================================
REJECT
================================ */

if(isset($_POST['reject'])){

$id=(int)$_POST['reject'];

$dbs->query("
UPDATE facility_loan
SET status='rejected',
rejected_at=NOW()
WHERE loan_id=$id
");

echo "<div class='alert alert-warning'>Permohonan ditolak</div>";

}

/* ================================
DELETE
================================ */

if(isset($_POST['delete'])){

$id=(int)$_POST['delete'];

$dbs->query("
DELETE FROM facility_loan
WHERE loan_id=$id
");

echo "<div class='alert alert-success'>Data berhasil dihapus</div>";

}

/* ================================
AMBIL DATA
================================ */

$query=$dbs->query("
SELECT *
FROM facility_loan
ORDER BY loan_id DESC
");

?>

<style>

.badge{
padding:4px 8px;
border-radius:4px;
color:#fff;
font-size:12px;
}

.badge-pending{background:#f0ad4e}
.badge-approved{background:#28a745}
.badge-rejected{background:#d9534f}

</style>


<div class="menuBox">
<div class="menuBoxInner circulationIcon">

<div class="per_title">
<h2>Peminjaman Fasilitas</h2>
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

<?php
$no=1;
while($row=$query->fetch_assoc()){
?>

<tr>

<td><?= $no++ ?></td>

<td><?= $row['borrower_name'] ?></td>

<td><?= $row['borrower_class'] ?></td>

<td><?= $row['activity_name'] ?></td>

<td>
<?= ucfirst($row['location_type']) ?> - <?= $row['location_name'] ?>
</td>

<td style="white-space:pre-line">

<?php

$items=explode(';',$row['items']);

foreach($items as $item){

$item=trim($item);

if($item!=''){
echo "• ".$item."<br>";
}

}

?>

</td>

<td><?= $row['start_datetime'] ?></td>

<td>

<?php

if($row['status']=='approved'){
echo "<span class='badge badge-approved'>approved</span>";
}
elseif($row['status']=='rejected'){
echo "<span class='badge badge-rejected'>rejected</span>";
}
else{
echo "<span class='badge badge-pending'>pending</span>";
}

?>

</td>

<td>

<?php

if($row['approved_at']){
echo $row['approved_at'];
}
elseif($row['rejected_at']){
echo $row['rejected_at'];
}
else{
echo '-';
}

?>

</td>

<td>

<?php if($row['status']=='pending'){ ?>

<form method="post" action="<?= $_SERVER['REQUEST_URI']; ?>" style="display:inline">

<input type="hidden" name="approve" value="<?= $row['loan_id'] ?>">

<button class="btn btn-success btn-sm">
Approve
</button>

</form>


<form method="post" action="<?= $_SERVER['REQUEST_URI']; ?>" style="display:inline">

<input type="hidden" name="reject" value="<?= $row['loan_id'] ?>">

<button class="btn btn-danger btn-sm">
Reject
</button>

</form>

<?php } else { ?>

<button class="btn btn-success btn-sm" disabled>
Approve
</button>

<button class="btn btn-danger btn-sm" disabled>
Reject
</button>

<?php } ?>


<?php if($row['status']!='pending'){ ?>

<button
type="button"
class="btn btn-primary btn-sm"
onclick="printSurat(<?= $row['loan_id'] ?>)">
Print
</button>

<?php } else { ?>

<button class="btn btn-secondary btn-sm" disabled>
Print
</button>

<?php } ?>


<form method="post"
action="<?= $_SERVER['REQUEST_URI']; ?>"
style="display:inline"
onsubmit="return confirm('Hapus data ini?')">

<input type="hidden" name="delete" value="<?= $row['loan_id'] ?>">

<button class="btn btn-dark btn-sm">
Hapus
</button>

</form>

</td>

</tr>

<?php } ?>

</tbody>
</table>

</div>
</div>


<script>

/* ===========================
POPUP INPUT NAMA PETUGAS
=========================== */

function printSurat(id){

let petugas = prompt("Masukkan Nama Petugas:");

if(petugas==null || petugas.trim()==""){

alert("Nama petugas wajib diisi");
return;

}

let url="../plugins/peminjaman_fasilitas/print.php?id="+id+"&petugas="+encodeURIComponent(petugas);

window.open(url,"_blank");

}

</script>
