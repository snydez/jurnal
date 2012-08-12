<?php
include_once("functions.php");
include_once( CLASSFOLDER . "/template.class.php");
include_once( CLASSFOLDER . "/postingan.class.php");
include_once( CLASSFOLDER . "/koment.class.php");

//baca session, apakah admin
$loginmode = $_SESSION["sesadmin"];


if (!$loginmode) { header ("Location: ./"); }

$theManage = new SimpleTemplate;

$results[] = $theManage->Define("_rangkaM_", TEMPLATEFOLDER . "/manage.html");
$results[] = $theManage->Define("_addnewpost_", TEMPLATEFOLDER ."/isi.html");
$results[] = $theManage->Define("_menus_", TEMPLATEFOLDER ."/menu.html");

/* foreach ($results as $result) {
	// echo $result;
 }
*/

$menu = $_GET["menu"];
$parampage = $_GET["hal"];
$paramIDKoment = $_GET["IDk"];

	$theManage->Assign("BaseFolder", BASEFOLDER );

/* handling menu
* 1. list postingan
* 2. list koment
* 3. isiposting 
**   mode edit
* 4. edit koment
*/

/* time zone  */
$TZOffset = GetTZOffset(); 

$t_time = time()-$TZOffset*60; #Counter adjust for localtime() 
$t_arr = localtime($t_time); 

/*  */

if ($menu==1) {
// kalau menu=1 (postingan), 
// baca database, ambil semua postingan, limit 5
   listpostingan();
	
} elseif ($menu==2) {
// kalau menu=1 (koment), 
// baca database, ambil semua postingan, limit 5
	listkoment();
	

} elseif ($menu==3) { 
// menu=3 adalah isiposting

	$mode  = $_GET["mode"];


	if ($mode=='edit') {
		$IDp = $_GET["IDp"];
		
		$strSQL = "select * from tblJurnal";
		$editpost = new Database($intDebug);
		$editpost->setstrSQL($strSQL);
		$editpost->setFilter("IDjurnal = " . $IDp);
		
		if (!$rsedit = $editpost->retrieve()) {
			echo $editpost->getError();
		} else {
			if (!$rs = mysql_fetch_assoc($rsedit)) {
				echo "error fetch";
			} else {
			
				$theManage->Assign("ID", $rs["IDJurnal"]);
				$theManage->Assign("JUDUL", $rs["strJudul"]);
				$theManage->Assign("TANGGAL", strftime("%d-%b-%Y",strtotime($rs["dtmTanggal"])));
				$theManage->Assign("JAM", strftime("%H:%m:%S",strtotime($rs["dtmTanggal"])));
				$theManage->Assign("DESKRIPSI", $rs["strDeskripsi"]);
				$theManage->Assign("ISIPOSTING", $rs["strJurnal"]);
				if ($rs["bolhasKoment"]) $bolehkoment = "CHECKED";
				
				$theManage->Assign("BOLEHKOMENT", $bolehkoment);
				if ($kat=retrieveKat($rs["IDKategori"])) {
					$theManage->Assign("KATEGORI", $kat);
				}
				if ($rs["bolDraft"]) $draft = "CHECKED";
				
				$theManage->Assign("DRAFT", $draft);
				
				$theManage->Assign("TAGS", retrieveTag($rs["IDJurnal"])); // elaborate this with tags of the selected post
			} //end error assoc
		} // end error retrieve

	} else {
		unset($kat);
		
		$tgl = date("d-M-Y");
		$jam = date("H:i");
		
		$theManage->Assign("JUDUL", "");
		$theManage->Assign("ID","");
		$theManage->Assign("TANGGAL", $tgl);
		$theManage->Assign("JAM", $jam);
		$theManage->Assign("DESKRIPSI", "");
		$theManage->Assign("ISIPOSTING", "");
		$theManage->Assign("BOLEHKOMENT", "CHECKED");
		if ($kat=retrieveKat("")) {
			$theManage->Assign("KATEGORI", $kat);
		}	
		$theManage->Assign("TAGS","");
	
	}
	

	$theManage->Assign("TAGSLIST", displayTagsList());
	
	$tmp = $theManage->Parse(_addnewpost_);
	$theManage->Assign("Manage", $tmp);

} elseif ($menu==4) {

	editkoment();
	
} else {
	$theManage->Assign("Manage", "<a href=\"". BASEFOLDER . "\">jurnal</a>");
}



unset($tmp);
//parsing menu
$theManage->Assign("URILINK", BASEFOLDER . "/manage.php");
$theManage->Assign("THEMENU", "utama");
//kalo querystring 'menu' masih kosong, berarti menu pertama yang di selected
if ($menu=='') {
	$theManage->Assign("SELECTED","selecxed"); }
else {
	$theManage->Assign("SELECTED","not-selected");
}

$tmp = $theManage->Parse(_menus_);
$theManage->Assign("URILINK","?menu=1");

//menu=1 adalah list post
if ($menu==1) {
	$theManage->Assign("SELECTED","selecxed");}
else {
	$theManage->Assign("SELECTED","not-selected");
}
$theManage->Assign("THEMENU","post list");
$tmp = $tmp . $theManage->Parse(_menus_);
$theManage->Assign("URILINK","?menu=2");

//menu=2 adalah koment  post
if ($menu==2) {
	$theManage->Assign("SELECTED","selecxed");}
else {
	$theManage->Assign("SELECTED","not-selected");
}

$theManage->Assign("THEMENU","koment list");
$tmp = $tmp . $theManage->Parse(_menus_);


// logout menu
$theManage->Assign("URILINK", "./login.php");
$theManage->Assign("THEMENU", "logout");
$theManage->Assign("SELECTED","not-selected");
$tmp = $tmp . $theManage->Parse(_menus_);


$theManage->Assign("Menu", $tmp);
//parsing seluruhnya, ke manage.html
$displayM = $theManage->Parse(_rangkaM_);


echo $displayM;

function listpostingan() {

	global $theManage;
	global $parampage;
	global $intDebug;
	
	
	
	$thePosts = new Postingan($intDebug);

	$thePosts->setPage($parampage);
	$thePosts->setMode("manage");
	$thePosts->setMaxPostperPage(10);
	$thePosts->displayDraft(1);
	$thePosts->setTemplate("listposting.html");
	
	// $theDraft = new Postingan($intDebug); masih belom tau gimana.
	
	
	
	$semuapostingan = $thePosts->readPosts();
	
//	if ($intDebug) echo $thePosts->getError();
	
	$halNavigasi = $thePosts->generateNavigasi();

	$listpostingan = "<form name=\"frm\" method=\"post\" id=\"frm\" action=\"hapus.php?what=1\">\n" . 	$semuapostingan . "\n";
	$listpostingan .= "<div class=\"clears\"><input type=\"submit\" name=\"btnDel\" value=\"delete selected\"></div>\n</form> <!-- end form //-->";
	$listpostingan .= "\n<div class=\"clears\">". $halNavigasi . "</div>\n";
	$listpostingan = $listpostingan . "\n<div class=\"addnewpost\">Create <a href=\"?menu=3\">NEW</a> post</div>\n";
	$theManage->Assign("Manage", $listpostingan);

	unset($thePosts);




}  // end listpostingan

function listkoment() {

	global $theManage;
	global $parampage;
	global $intDebug;
	
	$intDebug=0;

	$theKoment = new Koment($intDebug);
	
	$theKoment->setPage($parampage);
	$theKoment->setMaxKomentperPage(10);
	$theKoment->setTemplate("listkoment.html");
	
	$listkoment = $theKoment->readKoments();
	$halnavigasi = $theKoment->generateNavigasi();
	
	
	$listkoment = "<form name=\"frm\" method=\"post\" id=\"frm\" action=\"hapus.php?what=2\">\n<div>" . 	$listkoment. "\n</div>";
	$listkoment .= "<div class=\"clears\"><input type=\"submit\" name=\"btnDel\" value=\"delete selected\"></div>\n</form> <!-- end form //-->";
	$listkoment .= "<div class=\"clears\">&nbsp;</div>" .$halnavigasi . "";
	
	$theManage->Assign("Manage", $listkoment);
	
	//echo $theKoment->getError();

	
	unset($theKoment);
}


function editKoment () {

// menu = 3
	global $theManage;
	global $parampage;
	global $intDebug;
	global $paramIDKoment;
	
	
	
	$theKoment = new KomentForm($intDebug);
	$theKoment->setIDKoment($paramIDKoment);
	
	
	if (!$editedKoment = $theKoment->generateForm()) {
		echo "failed koment edit";
		
		
		return false;
	}
	
	$theManage->Assign("Manage", $editedKoment);
	
	unset($theKoment);
	
	

}

function retrieveKat($defaultKat){
	$strSQL = "select * from tblKategori";
	
	$katdb = new Database() ;
	
	$katdb->setstrSQL($strSQL);
	
	if ($x = $katdb->retrieve()) {
		while ($y = mysql_fetch_assoc($x) ) {
			$tmp .= "<option value=\"" . $y["IDKategori"] . "\" ";
			if ($defaultKat == $y["IDKategori"]) $tmp .= " selected ";
			$tmp .= ">";
			$tmp .= $y["strKategori"];
			$tmp .= "</option>\n";
			
		
		}  //end while

		return $tmp;
	
	} //end katdb -retrive
	else {
			return false;
	}
	
}

function retrieveTag($ID_) {


	$strSQL = "select * from relTag_Jurnal ";
	
	$dbTag = new Database($intDebug);
	$dbTag->setstrSQL($strSQL);
	$dbTag->setFilter("IDJurnal = " . $ID_ . "");
	
	$strKoma = "";
	if ($rs = $dbTag->retrieve()) {
		while ($row = mysql_fetch_assoc($rs)) {
			$strTags .= $strKoma . $row["IDTag"];
			if ($strKoma == "") $strKoma = ", ";
		}
	} else {
		return false;
	}
	
	unset($dbTag);
	return $strTags;
	


} 

function displayTagsList(){
	$strSQL = "select distinct IDTag from relTag_Jurnal ";
	
	$dbTags = new Database($intDebug);
	$dbTags->setstrSQL($strSQL);
	
	$strKoma = "";
	if ($rs = $dbTags->retrieve()) {
		while ($row = mysql_fetch_array($rs)) {
			$strTagList .= "<a href=\"#\" class=\"Tags\" title=\"". $row["IDTag"] . "\">";
			$strTagList .= $strKoma . $row["IDTag"];
			$strTagList .= "</a>\n ";
			//if ($strKoma == "") $strKoma = ", ";
		}
	} else {
		return false;
	}
	
	unset($dbTags);
	return $strTagList;
}
?>
