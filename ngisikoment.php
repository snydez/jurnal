<?php
include_once("functions.php");
include_once(CLASSFOLDER ."/database.class.php");

$addkoment = New Database;
$strKomentator = $_POST["txtNama"];
$strKoment = $_POST["txtKoment"];
$emailKomentator =  $_POST["txtemail"];
$IDJurnal = $_POST["hidID"];
$URIKomentator = $_POST["txtURI"];

$pattern = array("^http:[\/|\\]([a-zA-Z-]+)");
$replacewith = array("http://");

$intDebug = 0;
$checkcanKoment = new Database();

$checkcanKoment->setDebug($intDebug);

$strSQLcheck = "select strJudul, bolhasKoment from tblJurnal ";

$checkcanKoment->setstrSQL($strSQLcheck);
$checkcanKoment->setFilter("IDJurnal = " . $IDJurnal . " and bolhasKoment = 1");

$rowchecks = $checkcanKoment->retrieve();

if ($_SESSION["sessionID"]!=session_id()) {
	$allow = false;
} else {
	$allow = true;
}


if ($rowcheck = mysql_fetch_array($rowchecks) AND $allow ) {


	if ($rowcheck["bolhasKoment"]==1) {


		if (!eregi("http(s?)\:[\/|\\][\/|\\]",$URIKomentator)) {
			$URIKomentator = eregi_replace($pattern, $replacewith, $URIKomentator);
		}

		if (!eregi("http://", $URIKomentator)) {
		
			$URIKomentator = "http://" . $URIKomentator;
		}



		if (!eregi("@",$emailKomentator)) {
			$emailKomentator='';


		}

		if ($strKomentator && $strKoment) {

			$strSQL = "insert into tblKoment (strKomentator, dtmTanggal, strKoment, emailKomentator, IDJurnal, intType, URIKomentator, strAdditionalDescr)";
			$strSQL .= " values ('" . $strKomentator . "',now(),'" . replacechar($strKoment) . "','" . $emailKomentator . "'," . $IDJurnal . ",0,'" . $URIKomentator . "',". "'" .  $_SERVER['HTTP_REFERER']  . " | IP : " . $_SERVER['REMOTE_ADDR'] . "')";

			/** set cookie */
			setcookie("visitor[strNama]", $strKomentator, time() + (3600 * 24 * 30));
			setcookie("visitor[strE_Mail]", $emailKomentator, time() + (3600 * 24 * 30));
			setcookie("visitor[strURL]", $URIKomentator, time() + (3600 * 24 * 30));
	
			$addkoment->setstrSQL($strSQL);
			if (!$addkoment->create()) {
				echo $addkoment->getError($debug);
		
			} else {
				kirimemail($strKoment, $strKomentator . " for " . $IDJurnal . " - " . $rowcheck["strJudul"] );
				header("Location: " . BASEFOLDER . "/komen/" . $IDJurnal);
			}
		} else {

			echo "Nama dan koment harus diisi";
		}

	} else {
		echo "this blog post comment has been closed.";
	}

} else {
	echo "error. silahkan menggunakan tombol 'back' untuk kembali";
	
}
?>
