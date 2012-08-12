<? #managepost, edit, delete
include_once("functions.php");
include_once( CLASSFOLDER . "/database.class.php");


if ($chkpost = $_POST["chkdel"]) {

	$todelete = join(",", $chkpost);

	$strSQL = "delete from tblJurnal where IDJurnal in (" . $todelete . ")";
	
	$dbManage = New Database;
	$dbManage->setstrSQL($strSQL);
	if (!$dbManage->delete()) {
		echo $dbManage->getError();
	} else {
		header ("Location: manage.php?menu=1");
	}
	
}




?>