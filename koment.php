<?php
include_once("functions.php");
include_once( CLASSFOLDER . "/database.class.php");
include_once( CLASSFOLDER . "/template.class.php"); 

if ($_SESSION["sessionID"]!=session_id()) {
	$kosong = "kosong";
} else {
	$kosong = "";
}

$visitor = $_COOKIE["visitor"];

$theKoment = new SimpleTemplate;

$komenttemplate = getOption("komenttemplate");
$komentdetailtemplate = getOption("komentdetailtemplate");


$theKoment->Define("rangkakoment", TEMPLATEFOLDER . "/" . $kosong . $komenttemplate );
$theKoment->Define("komentdetail", TEMPLATEFOLDER . "/" . $komentdetailtemplate );


$IDpostingan = $_GET["IDp"];

if ($poskres = strpos($IDpostingan, "#")) {
	$IDpostingan = strleft($IDpostingan, 1,$poskres);
};


$DBkoments = new Database;

$strSQL = "select k.*, j.bolhasKoment, j.strJudul from tblKoment k, tblJurnal j ";

$DBkoments->setstrSQL($strSQL);
$DBkoments->setFilter("k.IDJurnal = j.IDJurnal AND k.IDJurnal  = " . $IDpostingan ."");
$DBkoments->setSort("IDKoment asc");



$DBkoments->setDebug(0);

$rowkoments = $DBkoments->retrieve();

$jmlkoments = ($DBkoments->getTotalRow() / 2);

unset($tmpKoment);
unset($isi);
$i=1;
while ($rowkoment = mysql_fetch_assoc($rowkoments)) {

	$theKoment->Assign("IDKoment", $rowkoment["IDKoment"]);
	$strNamadanURI = $rowkoment["strKomentator"];
	if ($lURI=$rowkoment["URIKomentator"]) {
		$strNamadanURI = "<a href=\"" . $lURI . "\" target=\"_blank\">" . $rowkoment["strKomentator"]  . "</a>";
	}	
	
	switch ($rowkoment["intType"]) {
		case 2:
			$z=' pingback';
			break;
		case 4:
			$z = ' trackback';
			break;
		default:
			$z = '';
	}
	
	$strKoment = replacelinebreak($rowkoment["strKoment"]);

	$strJudulPostingan = $rowkoment['strJudul'];
	$strJudulURI = JudulURI($rowkoment);

	$theKoment->Assign("Nama", $strNamadanURI);
	$theKoment->Assign("Koment", $strKoment);
	$theKoment->Assign("z", $z); // gradation background color;

	/* $theKoment->Assign("URI", $rowkoment["URIkomentator"]); */
	
	$tmpKoment = $theKoment->Parse(komentdetail);
	$isi = $isi . $tmpKoment;
	
	$i++;


}

$theKoment->Assign("ISIKOMENTS",$isi);


$hidID = $_GET["IDp"];
$theKoment->Assign("HIDID", $hidID);
$theKoment->Assign("JudulPostingan", $strJudulPostingan);
substr(strip_tags($row['strJurnal']),0, 20); 
$theKoment->Assign("URI", BASEFOLDER . "/id/" . $hidID . "/". $strJudulURI); 
$theKoment->Assign("CookieNama", $visitor[strNama]);
$theKoment->Assign("Cookiee-mail", $visitor[strE_Mail]);
$theKoment->Assign("CookieURI", $visitor[strURL]);
$theKoment->Assign("BaseFolder", BASEFOLDER);
$theKoment->Assign("Math1", rand(0,5));
$theKoment->Assign("Math2", rand(0,5));


$untukditampilkan = $theKoment->Parse("rangkakoment");

echo $untukditampilkan;

?>
