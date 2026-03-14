<?php
session_start();

define('INDEX_AUTH',1);
require '../../sysconfig.inc.php';

date_default_timezone_set('Asia/Jakarta');
global $dbs;

if($_SERVER['REQUEST_METHOD']!='POST'){
header("Location: ../../index.php");
exit;
}

/* CSRF */
if(!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])){
die("Invalid CSRF Token");
}

/* MATH */
if($_POST['math_answer'] != $_POST['math_correct']){
die("Math Challenge salah");
}

/* VALIDASI */
$required=[
'borrower_name',
'borrower_class',
'contact',
'activity_name',
'items',
'start_datetime',
'end_datetime'
];

foreach($required as $f){
if(empty($_POST[$f])){
die("Semua field wajib diisi");
}
}

/* ESCAPE */
$borrower_name=$dbs->escape_string($_POST['borrower_name']);
$borrower_class=$dbs->escape_string($_POST['borrower_class']);
$contact=$dbs->escape_string($_POST['contact']);
$supervisor=$dbs->escape_string($_POST['supervisor']);
$activity_name=$dbs->escape_string($_POST['activity_name']);
$location_type=$dbs->escape_string($_POST['location_type']);
$location_name=$dbs->escape_string($_POST['location_name']);
$items=$dbs->escape_string($_POST['items']);
$start_datetime=$dbs->escape_string($_POST['start_datetime']);
$end_datetime=$dbs->escape_string($_POST['end_datetime']);

/* INSERT DATA */

$dbs->query("INSERT INTO facility_loan
(
borrower_name,
borrower_class,
contact,
supervisor,
activity_name,
location_type,
location_name,
items,
start_datetime,
end_datetime,
status,
created_at
)
VALUES
(
'$borrower_name',
'$borrower_class',
'$contact',
'$supervisor',
'$activity_name',
'$location_type',
'$location_name',
'$items',
'$start_datetime',
'$end_datetime',
'pending',
NOW()
)");

$_SESSION['csrf_token']=bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Permohonan Berhasil</title>

<style>

body{
font-family:Arial;
background:#f0f2f5;
height:100vh;
display:flex;
align-items:center;
justify-content:center;
}

.box{
background:#fff;
padding:35px 40px;
border-radius:10px;
box-shadow:0 3px 15px rgba(0,0,0,0.1);
text-align:center;
max-width:420px;
}

h2{
margin-bottom:10px;
}

.count{
font-size:28px;
color:#1565d8;
font-weight:bold;
margin-top:10px;
}

</style>

</head>

<body>

<div class="box">

<h2>Permohonan Berhasil</h2>

<p>
Terima kasih telah mengajukan peminjaman fasilitas perpustakaan.
</p>

<p>
Halaman akan kembali ke beranda dalam
</p>

<div class="count">
<span id="countdown">5</span> detik
</div>

</div>

<script>

let time=5;

let timer=setInterval(function(){

time--;

document.getElementById("countdown").innerText=time;

if(time<=0){

clearInterval(timer);

window.location.href="../../index.php";

}

},1000);

</script>

</body>
</html>