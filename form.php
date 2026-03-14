<?php
session_start();
if(!defined('INDEX_AUTH')) define('INDEX_AUTH',1);
if(!isset($dbs)) require SB.'sysconfig.inc.php';
if(empty($_SESSION['csrf_token'])){$_SESSION['csrf_token']=bin2hex(random_bytes(32));}
date_default_timezone_set('Asia/Jakarta');
global $sysconf;
$library_name=$sysconf['library_name'];
$library_subname=$sysconf['library_subname'];
$logo=$sysconf['library_logo'];
if(!$logo){$logo="default/logo.png";}
if(isset($_POST['submit'])){
if(!isset($_POST['csrf_token'])||!hash_equals($_SESSION['csrf_token'],$_POST['csrf_token'])){die("Invalid request (CSRF)");}
if($_POST['math_answer']!=$_POST['math_correct']){die("Math Challenge salah");}
if(empty($_POST['borrower_name'])||empty($_POST['borrower_class'])||empty($_POST['contact'])||empty($_POST['activity_name'])||empty($_POST['items'])||empty($_POST['start_datetime'])||empty($_POST['end_datetime'])){die("Semua field wajib diisi");}
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
$dbs->query("INSERT INTO facility_loan(borrower_name,borrower_class,contact,supervisor,activity_name,location_type,location_name,items,start_datetime,end_datetime,status,created_at)VALUES('$borrower_name','$borrower_class','$contact','$supervisor','$activity_name','$location_type','$location_name','$items','$start_datetime','$end_datetime','pending',NOW())");
$_SESSION['csrf_token']=bin2hex(random_bytes(32));
$success=true;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Peminjaman Fasilitas Perpustakaan</title>
<style>
*{box-sizing:border-box;}
body{font-family:Arial;background:#f4f6f9;padding:30px;}
.form-box{width:98%;max-width:1100px;margin:auto;background:#fff;padding:45px;border-radius:12px;box-shadow:0 0 18px rgba(0,0,0,0.12);}
h2{text-align:center;margin-bottom:35px;font-size:32px;}
label{display:block;margin-top:18px;font-weight:bold;font-size:20px;}
input{width:100%;padding:18px;font-size:20px;margin-top:8px;border:1px solid #ccc;border-radius:8px;}
input.error{border:2px solid #e53935;}
button{margin-top:30px;width:100%;padding:20px;background:#1565d8;color:white;border:none;border-radius:10px;font-size:22px;cursor:pointer;}
button:hover{background:#0d47a1;}
.success{background:#d4edda;color:#155724;padding:16px;margin-bottom:20px;border-radius:8px;font-weight:bold;text-align:center;font-size:18px;}
.alert-box{background:#ffc107;color:#333;padding:14px;border-radius:8px;margin-bottom:20px;font-weight:bold;text-align:center;font-size:18px;}
#searchItem{font-size:20px;padding:18px;}
#searchResult{border:1px solid #ccc;max-height:250px;overflow-y:auto;background:#fff;margin-top:8px;border-radius:6px;}
.item-result{padding:14px;border-bottom:1px solid #eee;cursor:pointer;font-size:18px;}
.item-result:hover{background:#f1f1f1;}
.selected-item{display:flex;align-items:center;margin-top:10px;gap:10px;}
.selected-item span{flex:1;padding:10px 12px;border:1px solid #ddd;border-radius:6px;background:#fafafa;font-size:18px;}
.remove-btn{background:#e53935;color:white;border:none;padding:6px 12px;border-radius:6px;cursor:pointer;font-size:16px;}
.selected-item button{width:auto;margin:0;}
.location-option{display:flex;gap:30px;margin-top:10px;margin-bottom:10px;}
.location-option label{display:flex;align-items:center;gap:8px;font-weight:normal;font-size:18px;}
.help-text{display:block;font-size:14px;color:#666;margin-top:5px;}
.library-header{display:flex;align-items:center;justify-content:center;gap:18px;margin-bottom:25px;}
.library-logo{height:70px;width:auto;}
.library-text{text-align:left;}
.library-name{font-size:26px;font-weight:bold;letter-spacing:1px;}
.library-subname{font-size:16px;color:#666;}
.btn-home{display:block;margin-top:12px;width:100%;padding:18px;background:#6c757d;color:#fff;text-align:center;border-radius:10px;font-size:20px;text-decoration:none;}
.btn-home:hover{background:#495057;}
#supervisorResult{border:1px solid #ccc;max-height:200px;overflow-y:auto;background:#fff;margin-top:6px;border-radius:6px;}
.supervisor-item{padding:12px;border-bottom:1px solid #eee;cursor:pointer;}
.supervisor-item:hover{background:#f1f1f1;}
</style>
</head>
<body>

<div class="form-box">
<div class="library-header">
<img src="../../images/<?php echo $logo;?>" class="library-logo">
<div class="library-text">
<div class="library-name"><?php echo $library_name;?></div>
<div class="library-subname"><?php echo $library_subname;?></div>
</div>
</div>

<h2>Peminjaman Fasilitas Perpustakaan</h2>

<?php if(isset($success)){ ?>
<div class="success" id="successBox">Permohonan berhasil dikirim.<br>Form akan siap kembali dalam <span id="countdown">5</span> detik.</div>
<?php } ?>

<div id="formAlert" class="alert-box" style="display:none;">Semua field wajib diisi</div>

<form method="post" action="plugins/peminjaman_fasilitas/submit.php" id="loanForm">
<input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">

<label>Nama</label>
<input type="text" name="borrower_name" id="borrower_name" placeholder="Contoh: Azka Nurrachman" autofocus>
<small class="help-text">Tuliskan nama lengkap peminjam</small>

<label>Kelas/MGMP/Organisasi/Eskul</label>
<input type="text" name="borrower_class" placeholder="Contoh: XI TKJ 1">
<small class="help-text">Isi kelas/MGMP/Organisasi/Eskul</small>

<label>Nomor Kontak</label>
<input type="tel" name="contact" placeholder="Contoh: 081234567890">
<small class="help-text">Nomor yang bisa dihubungi (WhatsApp)</small>

<label>Penanggung Jawab (Guru / Pembina / Pimpinan / Ketua)</label>
<input type="text" id="searchSupervisor" placeholder="Ketik nama">
<div id="supervisorResult"></div>
<input type="hidden" name="supervisor" id="supervisorInput">
<small class="help-text">Guru / Pembina / Pimpinan / Ketua yang mengetahui kegiatan ini</small>

<label>Nama Kegiatan</label>
<input type="text" name="activity_name" placeholder="Contoh: Workshop Fotografi">
<small class="help-text">Tuliskan kegiatan yang akan dilakukan</small>

<label>Fasilitas yang Dipinjam</label>
<input type="text" id="searchItem" placeholder="Ketik nama fasilitas (contoh: Kamera)">
<small class="help-text">Ketik nama fasilitas untuk mencari, lalu pilih dari daftar</small>
<div id="searchResult"></div>
<div id="selectedItems"></div>
<input type="hidden" name="items" id="itemsInput">

<label>Lokasi</label>
<div class="location-option">
<label><input type="radio" name="location_type" value="indoor" required>Indoor</label>
<label><input type="radio" name="location_type" value="outdoor">Outdoor</label>
</div>

<input type="text" name="location_name" placeholder="Contoh: Ruang Multimedia" required>
<small class="help-text">Tuliskan lokasi kegiatan dilaksanakan</small>

<label>Tanggal & Jam Pinjam</label>
<input type="datetime-local" name="start_datetime">
<small class="help-text">Waktu mulai penggunaan fasilitas</small>

<label>Tanggal & Jam Selesai</label>
<input type="datetime-local" name="end_datetime">
<small class="help-text">Waktu selesai penggunaan fasilitas</small>

<label>Math Challenge</label>
<div id="mathQuestion" style="margin-top:5px;font-weight:bold;"></div>
<input type="number" name="math_answer" id="mathAnswer">
<small class="help-text">Jawab soal sederhana untuk verifikasi</small>
<input type="hidden" name="math_correct" id="mathCorrect">

<button type="submit" name="submit" id="submitBtn">Kirim Permohonan</button>
<a href="../../index.php" class="btn-home">Kembali ke Beranda</a>
</form>
</div>

<script>
let items=[];
document.getElementById("searchItem").addEventListener("keyup",function(){let q=this.value;if(q.length<2){document.getElementById("searchResult").innerHTML="";return;}fetch("/plugins/peminjaman_fasilitas/search_item.php?q="+q).then(r=>r.text()).then(data=>{document.getElementById("searchResult").innerHTML=data;});});
document.addEventListener("click",function(e){let item=e.target.closest(".item-result");if(item){let title=item.dataset.title;let code=item.dataset.code;let detail=item.dataset.detail;let text=title+" ("+code+"), "+detail;if(!items.includes(text)){items.push(text);renderItems();}document.getElementById("searchResult").innerHTML="";document.getElementById("searchItem").value="";}});
function renderItems(){let html="";items.forEach((item,index)=>{html+=`<div class="selected-item"><span>${item}</span><button type="button" class="remove-btn" onclick="removeItem(${index})">&times;</button></div>`;});document.getElementById("selectedItems").innerHTML=html;document.getElementById("itemsInput").value=items.join(";");}
function removeItem(index){items.splice(index,1);renderItems();}
function generateMath(){let a=Math.floor(Math.random()*10)+1;let b=Math.floor(Math.random()*10)+1;document.getElementById("mathQuestion").innerHTML="Berapa hasil: "+a+" + "+b+" ?";document.getElementById("mathCorrect").value=a+b;}
generateMath();

document.getElementById("searchSupervisor").addEventListener("keyup",function(){let q=this.value;if(q.length<2){document.getElementById("supervisorResult").innerHTML="";return;}fetch("/plugins/peminjaman_fasilitas/search_member.php?q="+q).then(r=>r.text()).then(data=>{document.getElementById("supervisorResult").innerHTML=data;});});
document.addEventListener("click",function(e){let item=e.target.closest(".supervisor-item");if(item){let name=item.dataset.name;let id=item.dataset.id;document.getElementById("searchSupervisor").value=name;document.getElementById("supervisorInput").value=id;document.getElementById("supervisorResult").innerHTML="";}});

let countdown=5;
document.getElementById("loanForm").addEventListener("submit",function(e){
let alertBox=document.getElementById("formAlert");alertBox.style.display="none";
let fields=['borrower_name','borrower_class','contact','activity_name','start_datetime','end_datetime'];
let valid=true;
fields.forEach(name=>{let field=document.querySelector('[name="'+name+'"]');field.classList.remove("error");if(field.value.trim()==""){field.classList.add("error");valid=false;}});
if(items.length==0){valid=false;}
let start=document.querySelector('[name="start_datetime"]').value;
let end=document.querySelector('[name="end_datetime"]').value;
if(start!=""&&end!=""&&end<=start){alert("Tanggal selesai harus lebih besar dari tanggal pinjam");valid=false;}
let answer=document.getElementById("mathAnswer").value;
let correct=document.getElementById("mathCorrect").value;
if(answer!=correct){alert("Jawaban Math Challenge salah");generateMath();document.getElementById("mathAnswer").value="";valid=false;}
if(!valid){alertBox.style.display="block";e.preventDefault();}
document.getElementById("submitBtn").disabled=true;
});

<?php if(isset($success)){ ?>
let form=document.getElementById("loanForm");let inputs=form.querySelectorAll("input,button");inputs.forEach(el=>el.disabled=true);
let timer=setInterval(function(){countdown--;document.getElementById("countdown").innerText=countdown;if(countdown<=0){clearInterval(timer);window.location.href="../../index.php";}},1000);
<?php } ?>
</script>
</body>
</html>
