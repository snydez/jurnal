<?php
include_once("param.php");
include_once( CLASSFOLDER . "/database.class.php");

function smiley($str) {

    $regsearch = array("' 8o'si",
					   "' :\)\)'si",
					   "' ;\('si",
					   "' :o'si",
					   "' :p'si",
					   "' :d'si",
					   "' :\)\)'si",
					   "' :\(\('si",
					   "' :\('si",
					   "' :\)'si",
					   "' ;\)'si",
					   "' ;p'si",
					   "'blobtongue.gif'si",
					   "'plaugh1.gif'si",
					   "'pcrying.gif'si",
					   "'pclapping.gif'si",
					   "'pdancing.gif'si",
					   "'ikari.gif'si"
					   );
	$regreplace = array('<img src="/img/shock.gif" alt="shock">',
						 '<img src="/img/evil.gif" alt="grin">',
						 '<img src="/img/nangis.gif" alt="cry">',
						 '<img src="/img/shock.gif" alt="shock">',
						 '<img src="/img/shy.gif" alt="shy">',
					    '<img src="/img/victory.gif" alt="v">',
						'<img src="/img/evil.gif" alt="grin">',
						'<img src="/img/nangis.gif" alt="cry">',
						'<img src="/img/sad.gif" alt="sad">',
						'<img src="/img/smile.gif" alt="smile">',
						'<img src="/img/wink.gif" alt="wink">',
						'<img src="/img/sing.gif" alt="sing">',
						"shy.gif",
						"sing.gif",
						"nangis.gif",
						"evil.gif",
						"happy.gif",
						"hurt.gif"
						 
						 );
						 
	return preg_replace ($regsearch, $regreplace, $str);

}

function sensor($strPostingan) {

	$regexsearch = array("'<sensor([^>]*)?>.*?</sensor>'si","'\[sensor\].*?\[/sensor\]'si")  ;
	$regexreplace = array("<img src=\"" . BASEFOLDER . "/" . TEMPLATEIMAGEFOLDER . "/sensor.jpg\">","<img src=\"" . BASEFOLDER . "/" . TEMPLATEIMAGEFOLDER . "/sensor.jpg\">");
	
	$strPostingan= preg_replace ($regexsearch, $regexreplace, $strPostingan);
	
	return $strPostingan;
}


function str_contains($haystack, $needle, $ignoreCase = false) {
    if ($ignoreCase) {
        $haystack = strtolower($haystack);
        $needle   = strtolower($needle);
    }
    $needlePos = strpos($haystack, $needle);
    return ($needlePos === false ? false : ($needlePos+1));
}


function spam($strreferer) {
	$bolspam = 0;
	if ($strreferer != "") {
	
		$strSQL = "select * from tblSpammer";
		$hasil = mysql_query($strSQL) 
		 		or die(mysql_error());
			while (($bolspam == 0) AND ($row=mysql_fetch_array($hasil))  ) {

				if ($row["isURL"]==1) {
					if (str_contains($strreferer, $row["URL_IP"], true)) $bolspam = 1;
				}  else {
					if (trim(substr($_SERVER[REMOTE_ADDR],0,strlen($row["URL_IP"]) ))==$row["URL_IP"]) $bolspam = 1;		
				}
				
			}	 //while

		
	} // if referer ke isi
	return $bolspam;
}

function yangdicari($thelink){

	$pos = strpos ($thelink, "google");
	if ($pos === false) { // note: three equal signs
		// not found ... 
		
		} else {
		
		if (ereg("[\?]+q=([^&]+)",$thelink,$outlink)) {
			$searchstring= urldecode($outlink[1]);
		}
	}
	$pos = strpos ($thelink, "yahoo");
	if ($pos === false) { // note: three equal signs
		// not found ... 
		
		} else {
		
		if (ereg('[.]*[va|p]=([^&]+)',$thelink,$outlink)) {
			$searchstring= urldecode($outlink[1]) ;
		
		}
	}
	$pos = strpos ($thelink, "alltheweb");
	if ($pos === false) { // note: three equal signs
		// not found ... 
		
		} else {
		
		if (ereg("\q=([^&]+)",$thelink,$outlink)) {
			$searchstring= urldecode($outlink[1]);
		}
	}
	$pos = strpos ($thelink, "msn.");
	if ($pos === false) { // note: three equal signs
		// not found ... 
		
		} else {
		
		if (ereg("\q=([^&]+)",$thelink,$outlink)) {
			$searchstring= urldecode($outlink[1]);
		}
	}
	$pos = strpos ($thelink, "aol.");
	if ($pos === false) { // note: three equal signs
		// not found ... 
		
		} else {
		
		if (ereg("\query=([^&]+)",$thelink,$outlink)) {
			$searchstring= urldecode($outlink[1]);
		}
	}
	
	$pos = strpos($thelink, "bing.");
	if ($pos === false) {
	} else {
		if (ereg("\q=([^&]+)", $thelink, $outlink)) {
			$searchstring= urldecode($outlink[1]);
		}
	}

	return $searchstring;

}


function readcategory($LINK_ID) {
	$resultz = '';
	$strSQLcat = 'select * from tblcategory where Link_ID = ' .$LINK_ID . ' order by idcategory' ;
	
	$qryCat = mysql_query($strSQLcat)
		or die($strSQLcat . " " . mysql_error());
	
	$arrCat = array();
	
	if ($qryCat) {
		while ($row = mysql_fetch_array($qryCat)) {
			$arrCat[] = "<a href=\"/cat/" . urlencode($row["category"]) . "\" rel=\"tag\">" .  $row["category"] . "</a>";
		}
		$resultz = implode (", ",$arrCat);
		
	} else  {
				$resultz  = mysql_error();
                }//end if
	return $resultz;
}

function displaytag($LINK_ID) {

	$temp = readcategory($LINK_ID);
	if ($temp != '') {
		echo "<div class=\"category\">\n";
		echo "tags : " . $temp;
		echo "</div>";
	}

}

function getOption($whatOption) {
/* function untuk membaca option
*/
	$DBopt = new Database();
	$strSQL = "select * from tblOption";

	$DBopt->setstrSQL($strSQL);
	$DBopt->setFilter("strOption = '" . $whatOption . "'");


	if ($hasilOpt = $DBopt->retrieve()) {
		unset($DBopt); 
		$rowOpt = mysql_fetch_assoc($hasilOpt);
		$thevalue = $rowOpt['strValue'];
	} else {
		$thevalue = $DBopt->getError();
	}
	return  $thevalue;

}

function debug($x) {
	echo "<span class=\"error\">" . $x . "</span>";
}

function leadingzero($thenumb) {
	if ($thenumb<=9) {
	    return ("0" . $thenumb);
	} else {
		return $thenumb;
	}
	

}


function JudulURI($row) {

	// kalo judulDeskripsi ada isinya, berarti forhyperlink isinya ya itu
	if (!$forhyperlink = urlencode(strip_tags($row['strJudulDeskripsi'])))  {
	// tapi kalo juduldeskripsi nya ga ada, 
	//   cek apakah strjudul ada isinya, kalo ada isinya, ya berarti judulpost ya itu
		if (!$judulpost = strip_tags($row['strJudul']) ) {
			//kalo ga ada judulpost, ambil dari postingan, 20 karakter.
			$judulpost = substr(strip_tags($row['strJurnal']),0, 20); 
			
		}

		$forhyperlink = strip_tags($judulpost);
	}
	
	
	   
	return onlychar($forhyperlink);   
	  
}


function onlychar($strX) {
	
	// ambil hanya karakter aja
	$regexsearch = "[^| |A-Za-z0-9-]";
	$regexreplace = ""; 
	
	$strX = strip_tags($strX);
	$strX = ereg_replace($regexsearch , $regexreplace , stripslashes($strX));
	$strX = str_replace(' ','-', $strX);
	$strX = urlencode($strX);
	
	return $strX;

}



function hardhostlink($strString) {

	$regexsearch = "a[ ]+href\=(\'|\")id";
	
	$textbody=ereg_replace($regexsearch ,"a href=\"http://". $_SERVER["HTTP_HOST"] . "/id",$strString);
	
	return $textbody;
	

}


function replacelinebreak($strX) {

 
	//replace line break with <br/>
	//replace double line break with <P>
	$strX = str_replace(chr(13).chr(10).chr(13).chr(10), '<p>', $strX);
	$strX = str_replace(chr(13).chr(10), '<br/>', $strX);

	return $strX;
}


function GetTZOffset() { 
  $Offset = date("O", 0); 
   
  $Parity = $Offset < 0 ? -1 : 1; 
  $Offset = $Parity * $Offset; 
  $Offset = ($Offset - ($Offset % 100))/100*60 + $Offset % 100; 

  return $Parity * $Offset; 
} 


function curPageURL() {
// retrieve current page url
	 $pageURL = 'http';
	 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if ($_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
}

function replacechar($txtContent) {

	return str_replace("'","\'",$txtContent);

}

function kirimemail($to, $msg) {

    ini_set("SMTP", "mail.snydez.com:26");
    ini_set("sendmail_from", "daftar@snydez.com");

    $message = $msg . ":\r\nSMTP = mail.snydez.com\r\nsmtp_port = 26\r\nsendmail_from = daftar@snydez.com";

    $headers = "From: daftar@snydez.com.com";


    mail($to, "koment", $message, $headers);


}

?>
