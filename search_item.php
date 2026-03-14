<?php

define('INDEX_AUTH',1);
require '../../sysconfig.inc.php';

global $dbs;

$q = isset($_GET['q']) ? $dbs->escape_string($_GET['q']) : '';

if(strlen($q) < 2){
exit;
}

$query=$dbs->query("
SELECT 
biblio.title,
biblio.spec_detail_info,
item.item_code
FROM item
LEFT JOIN biblio ON biblio.biblio_id=item.biblio_id
LEFT JOIN mst_gmd ON mst_gmd.gmd_id=biblio.gmd_id
WHERE
mst_gmd.gmd_name='Fasilitas Perpustakaan'
AND biblio.title LIKE '%$q%'
LIMIT 10
");

while($row=$query->fetch_assoc()){

$title = htmlspecialchars($row['title']);
$code = htmlspecialchars($row['item_code']);
$detail = htmlspecialchars($row['spec_detail_info']);

echo "<div class='item-result'
data-title=\"$title\"
data-code=\"$code\"
data-detail=\"$detail\"
style='padding:6px;border-bottom:1px solid #ddd;cursor:pointer;background:#fff'>";

echo "<b>$title</b><br>";
echo "<small>$code</small>";

if($detail){
echo " | <small>$detail</small>";
}

echo "</div>";

}