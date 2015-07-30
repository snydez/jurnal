<?php
/* get the referer */
// $strreferer =  strtolower($_SERVER['HTTP_REFERER']);

/* entar dulu deh 
if (spam($strreferer)==1) {
	header ("HTTP/1.0 404 Not Found");
}
*/

session_start();



$userlogin = 'snydzcom_bel';			// 'snydez';
$userpwd = 'snydzcom_bel2';			// 'snydez';
$dbname = 'snydzcom_dbjurnalv2';

define("CLASSFOLDER","clasz", true);
define("TEMPLATEFOLDER","templatez", true);
define("TEMPLATEIMAGEFOLDER","templatez/tximg", true);
define("BASEFOLDER","", true);  // jika diinstal di bawah root domain , kosongkan isi basefolder





?>
