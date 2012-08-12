<?php
include_once("functions.php");
include_once(CLASSFOLDER ."/database.class.php");


if ($chkpost = $_POST["chkdel"]) {

	if ($whattodel = $_GET["what"]) {
		$todelete = join(",", $chkpost);  // baca checkbox ID yang mau didelete, gabunging jadi string dengna koma
		
		if ($whattodel==1) {
			$strSQL = "delete from tblJurnal where IDJurnal in (" . $todelete . ")";
		} elseif ($whattodel==2) {
			$strSQL = "delete from tblKoment where IDKoment in (" . $todelete . ")";
		}
		
		$dbManage = New Database;
		$dbManage->setstrSQL($strSQL);
		if (!$dbManage->delete()) {
			echo $dbManage->getError();
		} else {
			header ("Location: manage.php?menu=" . $whattodel);
		}
		unset($dbManage);
		
	} else {
		echo "nothing to delete";
	}
}


?>