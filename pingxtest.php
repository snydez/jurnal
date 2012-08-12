<?php 
include_once("functions.php");
include_once( CLASSFOLDER ."/database.class.php");
include_once("pingbackclient.php");

$txtIsi = "testing pingback " . time() ;

$hyperlink = "http://jurnal.snydez.com/id/1/newbies";

$sp = send_pingback(stripslashes($txtIsi), $hyperlink,1);


?>

