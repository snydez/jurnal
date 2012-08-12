<?php header("X-Pingback: http://jurnal.snydez.com/pingback.php");
include_once("/jurnal/functions.php");
include_once("/jurnal/pingbackclient.php");
include_once("/jurnal/clasz/database.class.php");

$txtIsi = "testing pingback " . time() ;

$hyperlink = "http://jurnal.snydez.com/id/1/newbies";

$sp = send_pingback(stripslashes($txtIsi), $hyperlink,1);


?>

