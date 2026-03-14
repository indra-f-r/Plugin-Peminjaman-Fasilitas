<?php

define('INDEX_AUTH',1);
require '../../sysconfig.inc.php';

global $dbs;

$q = isset($_GET['q']) ? $dbs->escape_string($_GET['q']) : '';

if(strlen($q)<2){
exit;
}

$query=$dbs->query("
SELECT member_id,member_name
FROM member
WHERE member_name LIKE '%$q%'
LIMIT 10
");

while($row=$query->fetch_assoc()){

$name=htmlspecialchars($row['member_name']);
$id=htmlspecialchars($row['member_id']);

echo "<div class='supervisor-item'
data-name=\"$name\"
data-id=\"$id\">";

echo "<b>$name</b><br>";
echo "<small>$id</small>";

echo "</div>";
}