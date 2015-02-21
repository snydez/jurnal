<?php
include_once("functions.php");
include_once(CLASSFOLDER ."/database.class.php");
include_once("pingbackclient.php");

if ($_POST["txtTanggal"]!="") {
	$postdate = getdate(strtotime($_POST["txtTanggal"]));
	
	if ($_POST["txtJam"]!="") {
		$postjam = getdate(strtotime($_POST["txtJam"]));
	} else {
		$postjam = getdate();
	}
//echo "post txttanggal";
	
	$txtTanggal = "'" . $postdate[year] . "-" . $postdate[mon] . "-" . $postdate[mday] . " " .  $postjam[hours] . ":" . $postjam[minutes] ."'";
} else {
	$txtTanggal = "now()";
//echo "post now";
}

$txtIDp = $_POST["txthidID"];
$txtJudul = $_POST["txtJudul"];
$txtIsi = $_POST["txtIsi"];
$txtDeskripsi = $_POST["txtDeskripsi"];
$chkKoment = $_POST["chkKoment"];
$chkDraft = $_POST["chkDraft"];
$txtIDKategori = $_POST["cboKat"];
$txtTags = $_POST["txtTags"];
$txtuserID = $HTTP_SESSION_VARS["userID"] ;

if(!isset($chkDraft)) $chkDraft = 0;
if(!isset($chkKoment)) $chkKoment = 0;


$intDebug = 0;

$ngisi = new Database($intDebug);

if (!$txtIDp) {

	if (!isset($txtJudul)) $txtJudul = "tak berjudul";
	
	$strSQL = "insert into tblJurnal (dtmTanggal, strJudul, strJurnal, strDeskripsi , bolhasKoment, IDKategori, userID , strJudulDeskripsi, bolDraft)";
	$strSQL .= " values (" . $txtTanggal . ",'" . $txtJudul . "','" . $txtIsi . "','" . $txtDeskripsi . "'," . $chkKoment .",'" . $txtIDKategori  ."','" . $txtuserID   . "','"  . onlychar($txtJudul) .  "'," . $chkDraft . ")";
} else {
	$strSQL = "update tblJurnal SET ";
	$strSQL .= " dtmTanggal = " . $txtTanggal . ", ";
	$strSQL .= " dtmModify = now(), ";
	$strSQL .= " strJudul = '" . $txtJudul . "', ";
	$strSQL .= " strJurnal = '" . setchar($txtIsi) . "', ";
	$strSQL .= " strDeskripsi = '" . setchar($txtDeskripsi) . "', ";
	$strSQL .= " bolhasKoment = " . $chkKoment . ", ";
	$strSQL .= " bolDraft = " . $chkDraft . ", ";
	$strSQL .= " IDKategori = '" . $txtIDKategori . "' ";
	$strSQL .= " where  IDJurnal = " . $txtIDp . "";
	

}


$ngisi->setstrSQL($strSQL);

if (!$txtIDp) {
	if ($ngisi->create()) {
	

		$strInsertedID = mysql_insert_id();
	
		$hyperlink = $_SERVER["HTTP_HOST"] .  '/id/' . $strInsertedID . '/' . onlychar($txtJudul) ;

////	$hyperlink = 'http://jurnal.snydez.com/id/' . $strInsertedID . '/' . onlychar($txtJudul) ;
	
	
	

		if (tagging($txtTags, $strInsertedID)) { // insert tag corelate with this post
			echo "Postingan telah disave\n\r<br/>";
			echo "<a href=\"manage.php?menu=1\">post lists</a>";
		} else {
			echo "error";
		}
	} else {

		echo $ngisi->getError();
	} 

} else {
	if ($ngisi->update()) {
	
		$hyperlink = $_SERVER["HTTP_HOST"] .  '/id/' . $txtIDp . '/' . onlychar($txtJudul) ;

		if (tagging($txtTags, $txtIDp)) { // insert tag corelate with this post
			echo "Postingan telah diupdate\n\r<br/>";
			echo "<a href=\"manage.php?menu=1\">post lists</a>";
		} else {
			echo "errrr";
		}
	} else {

		echo $ngisi->getError();
	} 
}


if (substr($hyperlink,0,4) != "http") {$hyperlink = "http://" . $hyperlink;}  


if (!$chkDraft) $sp = send_pingback(stripslashes($txtIsi),  $hyperlink);
	
	

function tagging($txtTags_, $txtID_) {

	$intDebug = 1;

	
	$strSQLtagdel = "delete from relTag_Jurnal where IDJurnal = " . $txtID_ . "";
	
	$dbtag = new Database($intDebug);
	$dbtag->setstrSQL($strSQLtagdel);
	if (!$dbtag->delete()) 		return false;
	
	
	unset($dbtag);
	
	$strSQLtag = "insert into relTag_Jurnal (IDTag, IDJurnal) values ";
	$strKoma = "";
	
	foreach (explode(",", $txtTags_) as $strTag) {
		$strSQLtag .= $strKoma . "('" . trim($strTag) . "'," . $txtID_ . ")"; 
		if ($strKoma == "") $strKoma = ", "; // manipulasi supaya ada koma tiap values;
	}
	
	$dbitag = new Database($intDebug);
	$dbitag->setstrSQL($strSQLtag);
	if (!$dbitag->create()) 	return false;
	
	unset($dbitag);
	
	return true;
}


?>
