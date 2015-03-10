<?php header("X-Pingback: http://jurnal.snydez.com/pingback.php");
include_once("functions.php");
include_once( CLASSFOLDER . "/template.class.php");
include_once( CLASSFOLDER . "/postingan.class.php");
include_once( CLASSFOLDER . "/linklistwidget.class.php");
include_once( CLASSFOLDER . "/komentlistwidget.class.php");
include_once( CLASSFOLDER . "/aboutmewidget.class.php");
include_once( CLASSFOLDER . "/postlistwidget.class.php");
include_once( CLASSFOLDER . "/taglistwidget.class.php");
include_once( CLASSFOLDER . "/kategorilistwidget.class.php");
include_once( CLASSFOLDER . "/adsensewidget.class.php");
include_once( CLASSFOLDER . "/searchboxwidget.class.php");

$intDebug = 0;
//baca session, apakah admin
$editmode = $_SESSION["sesadmin"];
$_SESSION["sessionID"] = session_id(); 

//baca parameter querystring
$paramID = $_GET["IDp"];
$parampage = $_GET["hal"];
$paramtag = $_GET["tag"];


if ($_GET["kat"]) {
	$paramkategori  = $_GET["kat"];
	
} elseif ($_SESSION["kat"]) {
	$paramkategori = $_SESSION["kat"];	
} else {

	$paramkategori = 'Kat1';
}

$_SESSION["kat"] = $paramkategori;

if (!isset($paramID)) {
	if ($paramsearch = yangdicari($strreferer)) {
	// check apakah datang dari search engine <<- mesti dibuat dulu nih isi parameter
		
	} elseif ($_GET["btnGo"] == "Go") {
	// kalo engga, apakah datang dari search di page
		$paramsearch = $_GET["cari"];
		$parampage = 1; //reset kembali jadi halaman 1, kalau button go di click.
	} elseif ($_SESSION["sescari"]) {
	// kalo emang engga juga, apakah tadinya abis nyari?
		$paramsearch = $_SESSION["sescari"];
	}
} else {
	$paramsearch = "";
}

if ($_GET["btnClear"] == "clear" ) $paramsearch = "";


$_SESSION["sescari"] = $paramsearch;

/* 
if ($paramsearch) $_SESSION["sescari"]= $paramsearch;
if (!isset($_SESSION["sescari"])) {
	$_SESSION["sescari"] = $paramsearch;
} else {
	$paramsearch = $_SESSION["sescari"];
}

*/



// kalau ternyata gak ada parameter halaman, berarti halaman pertama
if (!isset($parampage)) $parampage = 1;




$useragent=$_SERVER['HTTP_USER_AGENT'];
if(preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))	$mobiletemplate = true;


if ($mobiletemplate) {
	$theTemplate = getOption("mobiletemplate");
} else {
	$theTemplate = getOption( $paramkategori . "template");
}



$WholeTemplate = new SimpleTemplate(0) ;
$WholeTemplate->Define("_Whole_", "" . TEMPLATEFOLDER . "/" . $theTemplate ."");

// Origingally --- $thePosts = new Postingan($intDebug);
// kalo ada parameterID, set ke Postingan class
//if (!isset($paramID)) {
//	$thePosts = new PostinganperCategory($intDebug);
//} else {
	$thePosts = new Postingan($intDebug);
	$thePosts->setSpecificID($paramID);
//}
// set current page

$thePosts->setPage($parampage);

if ($paramtag) $thePosts->setTag($paramtag);

$thePosts->displayDraft($editmode);

if (!$paramID && !$paramsearch) {
	// kalau langsung menuju specific IDjurnal, jangan filter by Kategori.
	if (!$paramkategori ) $paramkategori = 'Kat1'; 
	$thePosts->setKategori($paramkategori);
}


$theSearchBox = new SearchBoxWidget($intDebug);

if ($paramsearch<>"") {
	
	$thePosts->buildSearchQuery($paramsearch);
	$theSearchBox->setParamSearch($paramsearch);
	
}

$theSearchBox->setTarget($_SERVER['PHP_SELF']);  
$displayingSearchBox = $theSearchBox->retrieveWidget();
unset($theSearchBox);
$WholeTemplate->Assign("[WidgetSearchBox]", $displayingSearchBox );


$semuapostingan = $thePosts->readPosts();

$strJudulPostingan = $thePosts->getJudulPostingan();
$halNavigasi = $thePosts->generateNavigasi();


$tmpTwitterCard = $thePosts->loadTwitterCard();

unset($thePosts);
//masukkan isipostingan ke Template
$WholeTemplate->Assign("[Postingan]", $semuapostingan);



$WholeTemplate->Assign("[TwitterCard]", $tmpTwitterCard);





// TOP widget
$theKategories = new KategoriListWidget($intDebug);
$theKategories->setLimit(4);
$semuakategori = $theKategories->retrieveWidget();
unset($theKategories);
$WholeTemplate->Assign("[Kategori]", $semuakategori);

// LINKSSS
$theLinks = new LinkListWidget($intDebug);
$theLinks->setJudul("Links");
$theLinks->setLimit(12);
$theLinks->setKategori($_SESSION["kat"]);
$semualinks = $theLinks->retrieveWidget();
unset($theLinks);
$WholeTemplate->Assign("[WidgetLinkList]", $semualinks);

// KOMENTSS
$theKoments = new KomentListWidget($intDebug);
$theKoments->setJudul("Komentator");
$theKoments->setKategori($_SESSION["kat"]);
$semuakoments = $theKoments->retrieveWidget();
unset($theKoments);
$WholeTemplate->Assign("[WidgetKomentarList]", $semuakoments);

// POSTSSS
$thePrevPosts = new PostListWidget($intDebug);
$thePrevPosts->setJudul("Lainnya");
$thePrevPosts->setKategori($_SESSION["kat"]);
$thePrevPosts->setSpecificID($paramID);
$semuaprevposts = $thePrevPosts->retrieveWidget();

unset($thePrevPosts);
$WholeTemplate->Assign("[WidgetPostList]", $semuaprevposts);

//  ABOUTME 

$theLinksAboutMe = new AboutMeWidget($intDebug);
// $theLinksAboutMe->setJudul("About Me");
$theMe = $theLinksAboutMe->retrieveWidget();
unset($theLinksAboutMe);
$WholeTemplate->Assign("[WidgetAboutMe]", $theMe);


//  ADSENSE

$theAdsense = new AdsenseWidget($intDebug);
$theIklan = $theAdsense->retrieveWidget();
unset($theAdsense);
$WholeTemplate->Assign("[WidgetAdsense]", $theIklan);


// TAGSSS
$theTags = new TagListWidget($intDebug);
$theTags->setJudul("TagCloud");
$theTags->setKategori($paramkategori);
$semuatags = $theTags->retrieveWidget();
unset($theTags);
$WholeTemplate->Assign("[WidgetTagList]", $semuatags);



//TAMPILKAN!!!!
$headertitle = getOption("blogTitle");

if (isset($paramID)) {
	$headertitle .= " ~ " . $strJudulPostingan;
	$rsskoment = "<link rel=\"alternate\" type=\"application/rss+xml\" href=\"". BASEFOLDER . "/feed/koment/" . $paramID ."/rss20\" title=\"" . $strJudulPostingan . "\" />";
} else {
	$rsskoment = "";
}



$WholeTemplate->Assign("[BlogTitle]", $headertitle);
$WholeTemplate->Assign("[Halaman]", $halNavigasi);
$WholeTemplate->Assign("[BaseFolder]", BASEFOLDER );
$WholeTemplate->Assign("[RSSKoment]", $rsskoment );

if ($all = $WholeTemplate->Parse("_Whole_")) {
	echo $all;
} else {
	echo $WholeTemplate->getError();
}

unset($WholeTemplate);


?>
