<? 
include_once('param.php');
include_once( CLASSFOLDER . "/trackback_cls.php");
include_once( CLASSFOLDER . "/Snoopy_class.php");
include_once( CLASSFOLDER . "/database.class.php");



$editmode= $_SESSION["sesadmin"];
$BlogTitle = 'the Days After';
$gue = 'snydez';
$enco = 'UTF-8';

$id = $_GET["id"];
$__mode = $_GET["__mode"];
if ($__modex = $_POST["__modex"]) $__mode='';

// receiving trackback
if ($__mode == "track") {
	header('Content-Type: text/xml');
	
	$trackback = new Trackback($BlogTitle, $gue, $enco);

	$receivetrack = new Database($intDebug);
	
	$tb_id = $trackback->get_id; // The id of the item being trackbacked

    $tb_url = $trackback->url; // The URL from which we got the trackback
    $tb_title = $trackback->title; // Subject/title send by trackback
    $tb_expert = $trackback->expert; // Short text send by trackback
    $tb_blogname = $trackback->blogname;
	
	parse_str($tb_url, $x);


	$hasil = (spam($tb_title) || spam($tb_url) || spam($tb_expert) || spam($tb_blogname));
        if ($x['check']!='snydez') {$hasil = $hasil || 1;}

	if ($hasil==0) {
		if ($tb_url && $tb_expert) {
	

	$strSQL = "insert into tblKoment (strKomentator, dtmTanggal, strKoment, emailKomentator, IDJurnal, intType, URIKomentator)";
	$strSQL .= " values ('" . $tb_blogname . "',now(),'" . $tb_expert . "','noemail@trackba.ck'," . $tb_id . ",4,'" . $tb_url . "')";


	$receivetrack->setstrSQL($strSQL);
	
			if ($receivetrack->create() ) {
				echo $trackback->recieve(true);
			} //
			else {
				echo $trackback->recieve(false, 'cannot save');
			}//else hasillagi
	
		} else {
	
		echo $trackback->recieve(false, 'please provide your URL and Excerpt');
	
		}   
	 } else {
		echo $trackback->recieve(false, 'die! you spam!');
     }



} //$mode track
elseif ($__mode == "ping" && $editmode == "you're login")  { 
// going to send trackback
	$strpickposted = "select strJudul , strDeskripsi , strJudul, strJudulDeskripsi from tblJurnal";
	$strfilterpost = " IDJurnal = " . $id ;

	$picking = new Database($intDebug);
	$picking->setstrSQL($strpickposted);
	$picking->setFilter($strfilterpost);

	if ($rows=$picking->retrieve() AND $picking->getTotalRow()==1) {
		$row = mysql_fetch_assoc($rows);			
		$judul = $row['strJudul'];
		$strExcerpt = $row['strDeskripsi'];
		$strJudul = $row['strJudul'];
		$strJudulDeskripsi = $row['strJudulDeskripsi'];	
	

	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Ping and Track</title>
</head>

<body>
<form method="post">
<input type="hidden" name="__modex" value="send_ping" />
<table border="0" cellspacing="3" cellpadding="0">
<tr><td>TrackBack Ping URL:</td><td><input name="url" size="60" /></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td>Title:</td><td><input name="title" size="35" value="<? echo $judul ?>" /></td></tr>
<tr><td>Blog name:</td><td><input name="blog_name" size="35" value="the Days After" /></td></tr>
<tr><td>Excerpt:</td><td><textarea name="excerpt" cols="60" rows="4"><? echo $strExcerpt ?></textarea></td></tr>
<tr><td>Permalink URL:</td><td><input name="perma_url" size="60" value="http://jurnal.snydez.com/id/<? echo $id . '/' . urlencode(strip_tags($strJudulDeskripsi)) ?>"/></td></tr>
</table>
<input type="submit" value="pingx">&nbsp; <input onclick="javascript:back(0)" value="back" type="button">
</form>
</body>
</html>
<?
		} //hasil SQL
}  // __modeping

elseif ($__modex == "send_ping" && $editmode == "you're login") {

$title=$_POST["title"];
$perma_url=$_POST["perma_url"];
$BlogTitle= $_POST["blog_name"];
$excerpt = $_POST["excerpt"];
$url = $_POST["url"];

	$snoopy = new Snoopy();
	$submitURL = $url;
	$submitVars['Content-Type'] = 'application/x-www-form-urlencoded';
	$submitVars['title']        = $title;
	$submitVars['url']          = $perma_url;
	$submitVars['blog_name']    = $BlogTitle;
	$submitVars['excerpt']      = stripslashes($excerpt);
	
	$snoopy->submit($submitURL, $submitVars);
	$pingReply = $snoopy->results;
	
	// search through XML reply for any ping errors
	if (ereg('<error>([01])</error>', $pingReply, $pieces)) {
		$pingError = $pieces[1];
		}
	else {
		$pingError = 1;
		}
		
	if (!$pingError) {
		$message = 'Trackback was successful!';
		}
	else {
		if (ereg('<message>(.{0,})</message>', $pingReply, $pieces)) {
			$message = 'Error:&nbsp; '.$pieces[1];
			}
		else {
			$message = 'Error:&nbsp; Unknown';
			}
		}
	echo '<p class="ping">'.$message.'</p>'."\n";
	

	

} //endif _mode sendping


 ?>

				