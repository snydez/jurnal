<?php
include_once("param.php");

$conn = mysql_pconnect("localhost",$userlogin ,$userpwd)
   or die ('connect error');

mysql_select_db($dbname)
  or die ('db error');
?>