<?php 
include_once("functions.php");
include_once( CLASSFOLDER . "/template.class.php");
include_once( CLASSFOLDER . "/database.class.php");

$intDebug = 0;
unset($_SESSION["sesadmin"]);
unset($_SESSION["userID"]);

$theTemplate = "logintemplate.html";
$LoginTemplate = new SimpleTemplate() ;
$LoginTemplate->Define("_Login_", "" . TEMPLATEFOLDER . "/" . $theTemplate ."");


if ($_POST["btnLogin"] == "Login") {
	// kalau merupakan hasil form post'
	$strSQL = "select * from tblUser ";
	$logining = new Database($intDebug);
	
	$logining->setstrSQL($strSQL);
	$logining->setFilter("(userID = '" . $_POST["txtUserID"] . "' OR stremail = '" . $_POST["txtUserID"]    . "')   and strPassword = '" . $_POST["txtPassword"] . "'");
	if ($logining->retrieve() AND $logining->getTotalRow()==1) {
		$_SESSION["sesadmin"] = "you're login";
		$_SESSION["userID"] = $_POST["txtUserID"];
		header ("Location: manage.php?" . $_SESSION["userID"] ."");

	} else {
		if ($intDebug) {
			$strErr = $logining->getError();
		}
		$strErr .= "<span class=\"error\">e-mail / user or password is incorrect</span>";

	
	}
	
}



$LoginTemplate->Assign("ERRMSG", $strErr);
$LoginTemplate->Assign("FOLDER", TEMPLATEFOLDER);
if ($theLogin = $LoginTemplate->Parse("_Login_") ) {
	echo $theLogin;
} else {
	if ($intDebug) {
		echo $theLogin->getError(); 
	}
	echo "Error parsing";
}

?>
