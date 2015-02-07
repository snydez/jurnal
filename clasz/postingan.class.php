<?php
/* filename: postingan.class.php
	author:   sonny dezerial e.mail@snydez.com
	functionality : to generate postingan, and then put it on the template 
	create: June 1, 2008
	modified : Aug 11, 2012

* Feb 14, 2011
 ** add trackback URI

* Apr 22, 2011
 ** fix random, using $_COOKIES instead of $HTTP_GET_COOKIES

* Aug 11, 2012
 ** try to implement the kategori

* aug 22, 2012
 ** try to fix search formula

*/

include_once("database.class.php");
include_once("template.class.php");
include_once("errors.class.php");
include_once("twittercard.class.php");

class Postingan {
	var $errP;
	var $IDparaml;
	var $currentPage;
	var $template;
	var $jmlMaxPage;
	var $intDebug;
	var $strJudulPostingan;
	var $strTemplateName;
	var $maxPostperPage;
	var $managemode;
	var $strPostFilter;
	var $bolDisplayDraft;
	var $strTag;
	var $strKategori;
	var $rownumber;
	var $strSearchCriteria;
	var $strPostFilterOR;
	var $twitterCard;
	var $tmpTwitterCard;
	
	function Postingan($intDebugl=0){
		
		$this->setDebug($intDebugl);
		$this->managemode = "normal";
		$this->errP = new Errorc; 
		$this->displayDraft();
	}
		
	function setMode($strMode) {
		// kalo masuk ke mode manage /login .. set strMode jadi "manage"
		$this->managemode = $strMode;
	}

	function setTemplate($fulltemplatename) {
		$this->strTemplateName = $fulltemplatename;
	}
	
	function assignTemplate() {
		// kalo belom set templatename nya, ambil dari default option database;
		if (!$this->strTemplateName) $this->strTemplateName = getOption("postingantemplate");
				
		$this->template = new SimpleTemplate;
		
		if (!$this->template->Define("_postingan_", "" . TEMPLATEFOLDER . "/". $this->strTemplateName . "")) {
			$this->template->setError("Cannot set template" . $this->strTemplateName);
			return false;
			
		} //end if
	} 
	
	function setSpecificID($IDspecific)
	{
		$this->IDparaml = $IDspecific;
		if ($this->intDebug) $this->errP->errors("ID: " . $IDspecific);
	}
	
	function setPage($intpageNo){
		if ($intpageNo>0) {
			$this->currentPage= $intpageNo;
		} else {
			$this->currentPage= 1;
		}
		
	}
	
	function setCriteria($strCriteria) {
		if (len($strCriteria)>0) {
			$this->strSearchCriteria = $strCriteria;
		} 
		
	}
	
	
	
	function displayDraft($showDraft=0) {
		global $editmode;
		if (isset($editmode))  $showDraft=1;
		$this->bolDisplayDraft = $showDraft;
		
		
	}
	
	function addFilter($strFilter) {	
		// untuk memfilter postingan yang akan di readPost
		
		if (isset($this->strPostFilter)) {
			$this->strPostFilter =  $strFilter . " AND " . $this->strPostFilter ;
		} else {
			$this->strPostFilter = $strFilter;
		}
	}
	
	function addORFilter($strFilter) {
		// untuk nambahinn OR filter.
		if (isset($this->strPostFilterOR)) {
			$this->strPostFilterOR =   "(" . $this->strPostFilterOR . " OR " . $strFilter . ")";
		} else {
			$this->strPostFilterOR = $strFilter;
		}
	
	}
	
	function buildSearchQuery($strKeywords) {
		// $regg = "/(\".*?\"|\'.*?\')/";
		$regg = "/(\"|\')[a-zA-Z0-9\ ]*?(\'|\")/";
		$regg = "/((\"[^\"]+\")|( [^ ]+ ))+/";
		
		$regg="/\"(.+?)\"|\'(.*?)\'|(\w+)/";
		
		preg_match_all($regg,$strKeywords, $theKeywords);
		
		
		foreach($theKeywords[0] as $keyword) {
			$petik = array("\"","'");
			$keyword = str_replace($petik , "", $keyword);
				$this->addORFilter("strJudul like '%" . $keyword . "%'");
				$this->addORFilter("strJurnal like '%" . $keyword . "%'");
		


		}
		
		$this->addFilter($this->strPostFilterOR);

	}
	
	
	
	function clearFilter($isORFilter = 0) {
		if ($isORFilter == 0) {
			$this->strPostFilter  = "";
			unset($this->strPostFilter);
		} else {
			$this->strPostFilterOR = "";
			unset($this->strPostFilterOR);
		
		}
	}
	
	function setDebug($debug = 0) {
		$this->intDebug = $debug;
	}
	
	function setTag($tagg) {
		$this->addFilter("tj.IDTag = '" . $tagg . "'");
		$this->strTag = $tagg;
	}
	
	function setKategori($katt) {
		// untuk memfilter based on kategori
		$this->addFilter("k.IDKategori = '" . $katt . "'");
		$this->strKategori = $katt;
	}
	
	
	function getError() {
		return $this->errP->getError();
	}
	
	function setMaxPostperPage($maxpost) {
		$this->maxPostperPage = $maxpost;
	}
	
	function readPosts(){
	/*	var $MaxPostperPage;
		var $strSQL;
		var $DB;
	*/
	
		//$this->clearFilter();
		
		$this->assignTemplate();
		// if maxpostperpage not set, then get it from default option in database
		if (!$this->maxPostperPage)  		$this->maxPostperPage = getOption("MaxPostperPage");
				
		$DB = new Database($this->intDebug);
		
		$strSQL = "select * from tblJurnal j ";
		if ($this->strTag<>'') {
			$strSQL .= " , relTag_Jurnal tj ";
			$this->addFilter("j.IDJurnal = tj.IDJurnal ");
			
		}
	
		//if ($this->strKategori<>'') {
			$strSQL .= ", tblKategori k ";
			$this->addFilter("k.IDKategori = j.IDKategori");
		//}
	
		$DB->setstrSQL($strSQL);
		
		
		if ($this->bolDisplayDraft==0) {
				$this->addFilter("j.bolDraft <> 1");
				
		}
		
		
		
		if (isset($this->IDparaml)) {
			// spesificID dikirim. tampilin hanya postingan tersebut aja, ga usah semua
			$this->addFilter("j.IDJurnal = " . $this->IDparaml . "");

			
			if (isset($this->strPostFilter)) $DB->setFilter($this->strPostFilter);		
		
		} else {
			// kalo engga ada spesifikID, maka tampilkan semua urut terbalik. 		
			
			$DB->setSort("j.IDJurnal desc");
			//batasi hanya sejumlah 'MaxposPerPage' yang tampil disatu halaman
			$DB->setLimit(0, $this->maxPostperPage);
			//tampilkan halaman yang dimau.
			$DB->setPage($this->currentPage);
			//kalo ada filter, misalnya tampilin yang published (non draft)
			if (isset($this->strPostFilter)) $DB->setFilter($this->strPostFilter);
			
			
						
									
		} //end function isset IDparam
		
		
		
		if ($rs = $DB->retrieve()){
			//kalau sukses membaca database
			$jumlahTotalPosts = $DB->getTotalRow();	
			
			if ($this->intDebug) $this->errP->errors("<p>total posts: " . $jumlahTotalPosts . "</p>");
			unset($isi);
			
			$this->rownumber = 1; // set firstrownum = 1
			while ($row = mysql_fetch_assoc($rs)){
				
				$isi .= $this->generateSinglePost($row);
				$this->rownumber++;  // increment rownumber
			} // end while		
			
			$this->jmlMaxPage = ceil($jumlahTotalPosts/ $this->maxPostperPage);
			
			
			// kalo nampilin global posting, check apakah perlu nampilin random post?
			if (!isset($this->IDparaml)) {
				$isi = $this->generateRandomPost() . $isi;
			}
			
				
		} else {
			$this->errP->errors("tidak bisa retrieve postingan");
			
		} // end if rs
			
			
		if ($this->intDebug) $this->errP->errors("<p>xx" . $DB->getstrSQL() . "</p>");  //debug
		
		unset($DB);
		return $isi;		
		
	} //end function readPosts

	function loadTwitterCard() {
		return $this->tmpTwitterCard;

	}
	

function generateSinglePost($rowl) {
/* funtion to generate each post based on $row
*/
	

	// ambil tanggal, untuk kemudian diambil tanggal, bulan, tahun nya 
	$postdate = getdate(strtotime($rowl["dtmTanggal"]));
	
	$strSQLKoment = "select * from tblKoment";
	
	$DBkoments = new Database($this->intDebug);
	
	$DBkoments->setstrSQL($strSQLKoment);
	$DBkoments->setFilter("IDJurnal = " . $rowl["IDJurnal"] . ""); //retrieve koment based on IDJurnal
	
	$hasilquerykoment = $DBkoments->retrieve();  // masih harus dijalankan, supaya mentriger pengisian value ke getTotalRow

	$this->strJudulPostingan = strip_tags($rowl['strJudul']);
	
	$this->template->Assign("Kategori", $rowl["IDKategori"]);
	$this->strKategori =  $rowl["IDKategori"];


	$this->template->Assign("IDp", $rowl["IDJurnal"] );
	$this->template->Assign("JudulPostingan", $rowl['strJudul']);
	$this->template->Assign("URIPostingan",  BASEFOLDER . "/id/" . $rowl["IDJurnal"] . "/" . JudulURI($rowl));
	$this->template->Assign("URIEdit" , BASEFOLDER . "/manage.php?IDp=" . $rowl["IDJurnal"] . "&mode=edit&menu=3");
	
$fullURIPostingan = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://".$_SERVER['SERVER_NAME'] . "/id/" . $rowl["IDJurnal"] . "/" . JudulURI($rowl); 
	
	$this->template->Assign("FullURIPostingan",  $fullURIPostingan);

	$isipostingan = $this->permakpostingan($rowl['strJurnal']);
	
	$this->template->Assign("IsiPostingan", $isipostingan);
			
			/// grab image on postingan, then put in as front image
	preg_match('<img(.*)?src=\"([^\"]+)>',$isipostingan,$imginjurnal);
	$this->template->Assign("ImgInPost", $imginjurnal[2]);
	
	unset($isipostingan);
	
	$this->template->Assign("TglPostingan", $postdate[mday]);
	$this->template->Assign("BulanPostingan", substr($postdate[month],0,3));
	$this->template->Assign("TahunPostingan", $postdate[year]);
	$this->template->Assign("JamPostingan", $postdate[hours] . ":" . leadingzero($postdate[minutes]));

	$this->template->Assign("Draft", $row["bolDraft"]);
	$this->template->Assign("JumlahKoment", $DBkoments->getTotalRow());
	$this->template->Assign("URIKoment", BASEFOLDER . "/komen/" . $rowl["IDJurnal"]);
	
	$this->template->Assign("URITrackback", BASEFOLDER . "/ping/" . $rowl["IDJurnal"]);
	
	// if ($rowl['userID']) {	
		// $this->template->Assign("Author", "<div class=\"author\">post by <strong>" . $rowl['userID'] ."</strong></div>" );
	// } else {
		// $this->template->Assign("Author","");
	// }
	
	$this->template->Assign("Author", $this->displayAuthor($rowl['userID']));
	
	$this->template->Assign("Tags", $this->generateTags($rowl["IDJurnal"]));
		
	$this->template->Assign("RowNum",  $this->rownumber);
	// darft bukannya harusnya cuma bisa diliat admin???
	if ($rowl["bolDraft"]==1) {
		$this->template->Assign("Draft" , "draft");
	}
	
	$tmpContent = $this->template->Parse("_postingan_");

	$twitterCard = new TwitterCard($debugl);
	$twitterCard->assignTemplate();
	$twitterCard->setCard("TitleTC",$rowl['strJudul']);
	$twitterCard->setCard("DescriptionTC","blabalbalbalb<p>blalablaba");
	$twitterCard->setCard("ImageTC",$imginjurnal[2]);
	$twitterCard->setCard("PermalinkTC",$fullURIPostingan);
	
	$this->tmpTwitterCard = $twitterCard->loadCard();

	

	unset($DBkoments);
	return $tmpContent;

}

function displayAuthor($userID) {
	
	if (getOption("displayauthor")==1 AND isset($userID)) {
		
		$dbUser = new Database($this->intDebug);
		$dbUser->setstrSQL("select * from tblUser");
		$dbUser->setFilter("userID = '" . $userID . "'");
		
		if ($rs = $dbUser->retrieve()) {
			$row = mysql_fetch_assoc($rs);
			return "<div class=\"author\">post by <strong>" . $row["strFirstName"] . " " . $row["strLastName"] . "</strong></div>";
			
		} else {
			$this->errP->errors("error displaying author");
			return "";
		}
		
		
		
	
	
	}
}

function generateRandomPost(){


$tmpContent = "";
$visitorl = $_COOKIE["visitor"];

	if (isset($visitorl)) {

		if ($visitorl['strE_Mail']<>"") {

			/* kalo ada cookies e-mail, 
				  cek apakah si komentator tersebut mengkoment di postingan terakhir?
				     kalo iya, tampilkan satu random post.
				     kalo engga, ya udah ga usah tampilin random post.
			*/
			
			$DBlast = new Database($this->intDebug);
			$DBlast->setstrSQL("select j.IDJurnal ,  k.emailKomentator from tblJurnal j left join tblKoment k on j.IDJurnal = k.IDJurnal");
			$DBlast->setFilter("j.IDJurnal = (select max(IDJurnal) from tblJurnal) and k.emailKomentator='" . $visitorl['strE_Mail'] . "'");

			if ($hasillast = $DBlast->retrieve()) {
				if ($DBlast->getTotalRow()>=1) {
			
					// kalo ternyata udah ngoment di postingan terakhir
					// tampilin random post
					
					$DBrandom = new Database($this->intDebug);
		
					$strSQL = "select j.* from tblJurnal j left join tblKoment k on j.IDJurnal = k.IDJurnal" ;
					$DBrandom->setstrSQL($strSQL);
					$strFilter = "j.strJudul <> '' and k.emailKomentator<>'"  . $visitorl['strE_Mail'] . "'";
					$DBrandom->setFilter($strFilter);
					$DBrandom->setSort("RAND()");
					$DBrandom->setLimit(0,1);
					
					if ($hasil= $DBrandom->retrieve()) {
						$row = mysql_fetch_assoc($hasil);
						$tmpContent = $this->generateSinglePost($row);
					} //if success reading table jurnal with random

					unset($DBrandom);
				} // end kalo udah ngoment di postingan terakhir
				
			} // end hasillast
			
			unset($DBlast);
		}  // end no $visitorlemail
		
		
	} // end isset $visitorl
	
	return $tmpContent;

	
}

function generateNavigasi()
/* function untuk menampilkan navigasi halaman
   halaman pertama, halaman berikut, halaman sebelumnya halaman terakhir
*/
{

	$paramPage = $this->currentPage;
	$jmlmaxpage= $this->jmlMaxPage;
	
	
	if ($this->intDebug) {
	
		$this->errP->errors("param page " . $paramPage);
		$this->errP->errors("jmlMaxpage " . $jmlmaxpage);
	}

	// if it is manage mode, pake querystring aja.
	if ($this->managemode == "manage") {
		$tempquerystr = $_SERVER['REQUEST_URI'] . "&hal=";
	} else {
		
		// if it is filetered by tag , reformat navigation short url, add /tag/
		if ($this->strTag) {
			$tagging = "/tag/" . $this->strTag ;
		
		} else {
		
			$tagging = '';
		}
		
		// kalao ada filtering dengan kategori 
		if ($this->strKategori) {
			$katting = "/kat/" . $this->strKategori;
			
		} else {
			$katting = '';
		}
		
		$tempquerystr = BASEFOLDER . $katting . $tagging . "/hal/";
		
	}
	
	
	
	if ($jmlmaxpage>1) {
	

 	
		if ($paramPage>1 and $paramPage < $jmlmaxpage) {
		

			
			$paging = $tempquerystr . "1";
			$querystr = "<a href=\"" . $paging . "\" class=\"first\" title=\"first\">pertama</a>&nbsp;";
		
			$paging = $tempquerystr . ($paramPage-1) ;
			$querystr .= "<a href=\"". $paging . "\" class=\"prev\" title=\"prev\">sebelumnya</a>&nbsp;";


			$paging = $tempquerystr . ($paramPage+1) ;
			$querystr .= "<a href=\"". $paging . "\" class=\"next\" title=\"next\">berikutnya</a>&nbsp;";


			$paging = $tempquerystr . $jmlmaxpage ;
			$querystr .= "<a href=\"". $paging . "\" class=\"last\" title=\"last\">terakhir</a>&nbsp;";

		
		} elseif ($paramPage==1) {
			
			$paging = $tempquerystr . "2";
			$querystr = "<a href=\"". $paging .  "\" class=\"next\" title=\"next\">berikut</a>&nbsp;";

			if ($jmlmaxpage>2) {
				$paging = $tempquerystr . $jmlmaxpage;
				
				$querystr .= "<a href=\"". $paging . "\" class=\"last\" title=\"last\">terakhir</a>&nbsp;";
			}
			
		} elseif ($paramPage == $jmlmaxpage) {
			$paging = $tempquerystr . "1";
			
			$querystr = "<a href=\"". $paging .  "\" class=\"first\" title=\"first\">pertama</a>&nbsp;";
			
			if ($jmlmaxpage>2) {

				$paging = $tempquerystr . ($jmlmaxpage-1) ;
				$querystr .= "<a href=\"" . $paging . "\" class=\"prev\" title=\"prev\">sebelumnya</a>&nbsp;";
	
			}
		
		}
	}
	
	return $querystr;
}

function generateTags($IDp) {

	$DBTags = new Database($this->intDebug);
	$strSQL = "select * from relTag_Jurnal";
	$DBTags->setstrSQL($strSQL);
	$DBTags->setFilter("IDJurnal = " . $IDp . "");
	
	if ($rs = $DBTags->retrieve()) {
		
		while ($row = mysql_fetch_assoc($rs)) {
			$tmpTags[] = "<a href=\"" . BASEFOLDER . "/kat/" . $this->strKategori  .  "/tag/" .urlencode($row["IDTag"]) . "\" ref=\"tag\" class=\"tagsonpost\"  />" . $row["IDTag"] . "</a>";
		}
		if (count($tmpTags) > 0) {
			$strTags = implode(", ", $tmpTags);	
			if ($this->intDebug) $this->errP->errors($DBTags->getstrSQL());
		} else {
		//	$this->errP->errors("No tag available");
		
		}
	} else {
		
		$this->errP->errors("cannot generate tags");
	}


	unset($DBtags);

	return $strTags;


}


function permakpostingan($strPostingan) {
  global $editmode;
  
  // ---- $strPostingan = smiley($strPostingan);  -- gak pake smiley
  
  if (!isset($editmode)) {
  /* kalo bukan admin, sensor! */
	$strPostingan = sensor($strPostingan);
  }
  
  return $strPostingan;
}

function getJudulPostingan() {
	return $this->strJudulPostingan;
}

} // end class


class PostinganperCategory extends Postingan {

	function readPosts() {
	
		$this->intDebug = 1;
		
		$this->assignTemplate();
		$DBKat = new Database($this->intDebug);
		
		$DBKat->setstrSQL("select * from  tblKategori ");
		if (!$rsKat = $DBKat->retrieve()) {
			$DBKat->errc->errors("cannot browse Kategori");
			return false;
			// if it is eerror, return false;	
		}
		
		$DBJurnal = new Database($this->intDebug);
		
		unset($isi);
		$this->rownumber = 1; // set firstrownum = 1
		while ($rowKat = mysql_fetch_assoc($rsKat)) {
			
			$DBJurnal->setstrSQL("select * from tblJurnal");
			$DBJurnal->setFilter("IDKategori = '" . $rowKat["IDKategori"] . "'");
			$DBJurnal->setSort("IDJurnal desc");
			$DBJurnal->setLimit(0,2);
			
			if (!$rsJ = $DBJurnal->retrieve()) {
				$DBJurnal->errc->errors("cannot browse Kategori");
				return false;
				// if it is eerror, return false;
			}

			
			
			while ($rowJ = mysql_fetch_assoc($rsJ)) {
				$isi .= $this->generateSinglePost($rowJ);
				$this->rownumber++; // incerement the rownumber
			
			}
			
			unset($rowJ);
			unset($rsJ);
			
		}
		
		unset($rowKat);
		unset($rsKat);
		
		unset($DBKat);
		unset($DBJurnal);
		
		return $isi;

	} //end of function REadPost;
	
} // end of Class

?>
