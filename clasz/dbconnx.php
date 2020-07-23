<?php
include_once("param.php");


global $conny;

$conny = mysqli_connect("localhost",$userlogin,$userpwd,$dbname) or die("gagal, database tidak ditemukan" . $dbname . " xaya");

// $conn = mysql_pconnect("localhost",$userlogin ,$userpwd)
//   or die ('connect error');

//mysql_select_db($dbname)
//  or die ('db error');
?>