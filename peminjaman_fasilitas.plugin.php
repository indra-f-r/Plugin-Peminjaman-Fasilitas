<?php
/**
 * Plugin Name: Peminjaman Fasilitas
 * Plugin URI: https://github.com/indra-f-r
 * Description: Plugin untuk Peminjaman Fasilitas Perpustakaan
 * Version: 1.1.0
 * Author: Indra Febriana Rulliawan
 * Author URI: https://github.com/indra-f-r
 */

$plugin=\SLiMS\Plugins::getInstance();
global $dbs;

/* =====================================
CEK / BUAT TABEL
===================================== */

$check=$dbs->query("SHOW TABLES LIKE 'facility_loan'");

if($check && $check->num_rows==0){

$sql="CREATE TABLE facility_loan(

loan_id INT AUTO_INCREMENT PRIMARY KEY,

borrower_name VARCHAR(100),
borrower_class VARCHAR(50),
contact VARCHAR(30),
supervisor VARCHAR(100),
activity_name VARCHAR(200),

items TEXT,

start_datetime DATETIME,
end_datetime DATETIME,

status VARCHAR(20),

loan_number VARCHAR(50),

created_at DATETIME,

approved_at DATETIME NULL,
rejected_at DATETIME NULL,
returned_at DATETIME NULL

) ENGINE=InnoDB";

$dbs->query($sql);

}

/* =====================================
CEK KOLOM TAMBAHAN (UPGRADE)
===================================== */

$columns=[];

$result=$dbs->query("SHOW COLUMNS FROM facility_loan");

while($row=$result->fetch_assoc()){
$columns[]=$row['Field'];
}

if(!in_array('approved_at',$columns)){
$dbs->query("ALTER TABLE facility_loan ADD approved_at DATETIME NULL");
}

if(!in_array('rejected_at',$columns)){
$dbs->query("ALTER TABLE facility_loan ADD rejected_at DATETIME NULL");
}

if(!in_array('returned_at',$columns)){
$dbs->query("ALTER TABLE facility_loan ADD returned_at DATETIME NULL");
}

if(!in_array('loan_number',$columns)){
$dbs->query("ALTER TABLE facility_loan ADD loan_number VARCHAR(50)");
}

if(!in_array('location_type',$columns)){
$dbs->query("ALTER TABLE facility_loan ADD location_type VARCHAR(20)");
}

if(!in_array('location_name',$columns)){
$dbs->query("ALTER TABLE facility_loan ADD location_name VARCHAR(200)");
}

/* =====================================
REGISTER MENU
===================================== */

$plugin->registerMenu(
'circulation',
'Peminjaman Fasilitas',
__DIR__.'/index.php'
);

/* =====================================
ROUTING OPAC
===================================== */

if (isset($_GET['p']) && $_GET['p'] == 'peminjaman') {
    if(!defined('INDEX_AUTH')) define('INDEX_AUTH',1);
    require __DIR__.'/form.php';
    exit;
}
