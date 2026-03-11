<?php

session_start();

define('INDEX_AUTH',1);
require '../../sysconfig.inc.php';

if(empty($_SESSION['csrf_token'])){
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

date_default_timezone_set('Asia/Jakarta');
global $dbs;
global $dbs;

if(isset($_POST['submit'])){

if(
!isset($_POST['csrf_token']) ||
!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
){
die("Invalid request (CSRF)");
}

if($_POST['math_answer'] != $_POST['math_correct']){
die("Math Challenge salah");
}

if(
empty($_POST['borrower_name']) ||
empty($_POST['borrower_class']) ||
empty($_POST['contact']) ||
empty($_POST['activity_name']) ||
empty($_POST['items']) ||
empty($_POST['start_datetime']) ||
empty($_POST['end_datetime'])
){
die("Semua field wajib diisi");
}

$borrower_name = $dbs->escape_string($_POST['borrower_name']);
$borrower_class = $dbs->escape_string($_POST['borrower_class']);
$contact = $dbs->escape_string($_POST['contact']);
$supervisor = $dbs->escape_string($_POST['supervisor']);
$activity_name = $dbs->escape_string($_POST['activity_name']);
$location_type = $dbs->escape_string($_POST['location_type']);
$location_name = $dbs->escape_string($_POST['location_name']);
$items = $dbs->escape_string($_POST['items']);
$start_datetime = $dbs->escape_string($_POST['start_datetime']);
$end_datetime = $dbs->escape_string($_POST['end_datetime']);

$dbs->query("
INSERT INTO facility_loan
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
)
");

/* regenerate csrf token */

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

$success=true;

}

?>

<!DOCTYPE html>
<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Peminjaman Fasilitas Perpustakaan</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:30px;
}

/* CONTAINER FORM */

.form-box{
width:98%;
max-width:1100px;
margin:auto;
background:#fff;
padding:45px;
border-radius:12px;
box-shadow:0 0 18px rgba(0,0,0,0.12);
}

/* JUDUL */

h2{
text-align:center;
margin-bottom:35px;
font-size:32px;
}

/* LABEL */

label{
display:block;
margin-top:18px;
font-weight:bold;
font-size:20px;
}

/* INPUT */

input{
width:100%;
padding:18px;
font-size:20px;
margin-top:8px;
border:1px solid #ccc;
border-radius:8px;
}

/* ERROR INPUT */

input.error{
border:2px solid #e53935;
}

/* BUTTON */

button{
margin-top:30px;
width:100%;
padding:20px;
background:#1565d8;
color:white;
border:none;
border-radius:10px;
font-size:22px;
cursor:pointer;
}

button:hover{
background:#0d47a1;
}

/* SUCCESS MESSAGE */

.success{
background:#d4edda;
color:#155724;
padding:16px;
margin-bottom:20px;
border-radius:8px;
font-weight:bold;
text-align:center;
font-size:18px;
}

/* ALERT */

.alert-box{
background:#ffc107;
color:#333;
padding:14px;
border-radius:8px;
margin-bottom:20px;
font-weight:bold;
text-align:center;
font-size:18px;
}

/* SEARCH RESULT */

#searchItem{
font-size:20px;
padding:18px;
}

#searchResult{
border:1px solid #ccc;
max-height:250px;
overflow-y:auto;
background:#fff;
margin-top:8px;
border-radius:6px;
}

.item-result{
padding:14px;
border-bottom:1px solid #eee;
cursor:pointer;
font-size:18px;
}

.item-result:hover{
background:#f1f1f1;
}

/* SELECTED ITEM */

.selected-item{
display:flex;
align-items:center;
margin-top:10px;
gap:10px;
}

.selected-item span{
flex:1;
padding:10px 12px;
border:1px solid #ddd;
border-radius:6px;
background:#fafafa;
font-size:18px;
}

.remove-btn{
background:#e53935;
color:white;
border:none;
padding:6px 12px;
border-radius:6px;
cursor:pointer;
font-size:16px;
}

.selected-item button{
width:auto;
margin:0;
}

/* LOCATION OPTION */

.location-option{
display:flex;
gap:30px;
margin-top:10px;
margin-bottom:10px;
}

.location-option label{
display:flex;
align-items:center;
gap:8px;
font-weight:normal;
font-size:18px;
}

/* HELPER TEXT */

.help-text{
display:block;
font-size:14px;
color:#666;
margin-top:5px;
}
</style>

</head>

<body>

<div class="form-box">

<h2>Peminjaman Fasilitas Perpustakaan</h2>

<?php if(isset($success)){ ?>

<div class="success" id="successBox">
Permohonan berhasil dikirim.<br>
Form akan siap kembali dalam <span id="countdown">3</span> detik.
</div>

<?php } ?>

<div id="formAlert" class="alert-box" style="display:none;">
Semua field wajib diisi
</div>

<form method="post" id="loanForm">

<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">

<label>Nama</label>
<input type="text" name="borrower_name" id="borrower_name" placeholder="Contoh: Azka Nurrachman">
<small class="help-text">Tuliskan nama lengkap peminjam</small>

<label>Kelas/MGMP/Organisasi/Eskul</label>
<input type="text" name="borrower_class" placeholder="Contoh: XI TKJ 1">
<small class="help-text">Isi kelas/MGMP/Organisasi/Eskul</small>

<label>Nomor Kontak</label>
<input type="tel" name="contact" placeholder="Contoh: 081234567890">
<small class="help-text">Nomor yang bisa dihubungi (WhatsApp)</small>

<label>Penanggung Jawab (Guru / Pembina / Pimpinan / Ketua)</label>
<input type="text" name="supervisor" placeholder="Contoh: Pak Dedi (Guru Multimedia)">
<small class="help-text">Guru / Pembina / Pimpinan / Ketua yang mengetahui kegiatan ini</small>

<label>Nama Kegiatan</label>
<input type="text" name="activity_name" placeholder="Contoh: Workshop Fotografi">
<small class="help-text">Tuliskan kegiatan yang akan dilakukan</small>

<label>Fasilitas yang Dipinjam</label>

<input type="text" id="searchItem" placeholder="Ketik nama fasilitas (contoh: Kamera)">

<small class="help-text">
Ketik nama fasilitas untuk mencari, lalu pilih dari daftar
</small>

<div id="searchResult"></div>
<div id="selectedItems"></div>

<input type="hidden" name="items" id="itemsInput">

<label>Lokasi</label>

<div class="location-option">

<label>
<input type="radio" name="location_type" value="indoor" required>
Indoor
</label>

<label>
<input type="radio" name="location_type" value="outdoor">
Outdoor
</label>

</div>

<input type="text" name="location_name" placeholder="Contoh: Ruang Multimedia" required>

<small class="help-text">
Tuliskan lokasi kegiatan dilaksanakan
</small>

<label>Tanggal & Jam Pinjam</label>
<input type="datetime-local" name="start_datetime">

<small class="help-text">
Waktu mulai penggunaan fasilitas
</small>

<label>Tanggal & Jam Selesai</label>
<input type="datetime-local" name="end_datetime">

<small class="help-text">
Waktu selesai penggunaan fasilitas
</small>

<label>Math Challenge</label>

<div id="mathQuestion" style="margin-top:5px;font-weight:bold;"></div>

<input type="number" name="math_answer" id="mathAnswer">

<small class="help-text">
Jawab soal sederhana untuk verifikasi
</small>

<input type="hidden" name="math_correct" id="mathCorrect">

<button type="submit" name="submit">
Kirim Permohonan
</button>

</form>

</div>

<script>

let items=[];

/* SEARCH */

document.getElementById("searchItem").addEventListener("keyup",function(){

let q=this.value;

if(q.length<2){
document.getElementById("searchResult").innerHTML="";
return;
}

fetch("search_item.php?q="+q)
.then(r=>r.text())
.then(data=>{
document.getElementById("searchResult").innerHTML=data;
});

});

/* PILIH ITEM */

document.addEventListener("click",function(e){

let item=e.target.closest(".item-result");

if(item){

let title=item.dataset.title;
let code=item.dataset.code;
let detail=item.dataset.detail;

let text=title+" ("+code+"), "+detail;

if(!items.includes(text)){
items.push(text);
renderItems();
}

document.getElementById("searchResult").innerHTML="";
document.getElementById("searchItem").value="";

}

});

/* RENDER */

function renderItems(){

let html="";

items.forEach((item,index)=>{

html+=`
<div class="selected-item">
<span>${item}</span>
<button type="button" class="remove-btn" onclick="removeItem(${index})">&times;</button>
</div>
`;

});

document.getElementById("selectedItems").innerHTML=html;

document.getElementById("itemsInput").value=items.join(";");

}

/* HAPUS ITEM */

function removeItem(index){

items.splice(index,1);

renderItems();

}

/* MATH */

function generateMath(){

let a=Math.floor(Math.random()*10)+1;
let b=Math.floor(Math.random()*10)+1;

document.getElementById("mathQuestion").innerHTML="Berapa hasil: "+a+" + "+b+" ?";

document.getElementById("mathCorrect").value=a+b;

}

generateMath();

/* VALIDASI */

document.getElementById("loanForm").addEventListener("submit",function(e){

let alertBox=document.getElementById("formAlert");
alertBox.style.display="none";

let fields=[
'borrower_name',
'borrower_class',
'contact',
'activity_name',
'start_datetime',
'end_datetime'
];

let valid=true;

fields.forEach(name=>{

let field=document.querySelector('[name="'+name+'"]');

field.classList.remove("error");

if(field.value.trim()==""){
field.classList.add("error");
valid=false;
}

});

if(items.length==0){
valid=false;
}

let start=document.querySelector('[name="start_datetime"]').value;
let end=document.querySelector('[name="end_datetime"]').value;

if(start!="" && end!="" && end<=start){
alert("Tanggal selesai harus lebih besar dari tanggal pinjam");
valid=false;
}

let answer=document.getElementById("mathAnswer").value;
let correct=document.getElementById("mathCorrect").value;

if(answer!=correct){
alert("Jawaban Math Challenge salah");
generateMath();
document.getElementById("mathAnswer").value="";
valid=false;
}

if(!valid){
alertBox.style.display="block";
e.preventDefault();
}

});

/* COUNTDOWN & LOCK FORM */

<?php if(isset($success)){ ?>

let countdown=3;

let form=document.getElementById("loanForm");
let inputs=form.querySelectorAll("input,button");

inputs.forEach(el=>el.disabled=true);

let timer=setInterval(function(){

countdown--;

document.getElementById("countdown").innerText=countdown;

if(countdown<=0){

clearInterval(timer);

form.reset();

items=[];
renderItems();

generateMath();

inputs.forEach(el=>el.disabled=false);

document.getElementById("successBox").style.display="none";

document.getElementById("borrower_name").focus();

}

},1000);

<?php } ?>

</script>

</body>
</html>