<?php
/* filename: koment.class.php
	author:   sonny dezerial e.mail@snydez.com
	functionality : to generate koment, and then put it on the template 
	create: 10:14 19/01/2009
	modify : 19:11 25/01/2009
*/

include_once("database.class.php");
include_once("template.class.php");
include_once("errors.class.php");

class Koment {

	var $intDebug;
	var $errP;
	var $strTemplateName;
	var $template;
	var $currentpage;
	var $strKomentFilter;
	var $intType;
	var $maxKomentperPage;
	
	function __construct($intDebugl=0) {
		$this->setDebug($intDebugl);
		$this->errP = new Errorc; 
	}

	
	function setDebug($debug = 0) {
		$this->intDebug = $debug;
	}
	
	function setTemplate($fulltemplatename) {
		$this->strTemplateName = $fulltemplatename;
	}
	
	function assignTemplate() {
		// kalo belom set templatename nya, ambil dari default option database;
		if (!$this->strTemplateName) $this->strTemplateName = getOption("komenttemplate");
				
		$this->template = new SimpleTemplate($this->intDebug);
		
		if (!$this->template->Define("_koment_", "" . TEMPLATEFOLDER . "/". $this->strTemplateName . "")) {
			$this->template->setError("Cannot set template" . $this->strTemplateName);
			return false;
			
		} else {
			return true;//end if
		}
	} 
	
	function setPage($intpageNo){
		if ($intpageNo>0) {
			$this->currentPage= $intpageNo;
		} else {
			$this->currentPage= 1;
		}
		
	}
	
	function addFilter($strFilter) {	
		// untuk memfilter koemnt yang akan di readKoments;
		
		if (isset($this->strKomentFilter)) {
			$this->strKomentFilter = $strFilter . " AND " . $this->strKomentFilter;
		} else {
			$this->strKomentFilter = $strFilter;
		}
	}
	
	function clearFilter() {
		$this->strKomentFilter  = "";
		unset($this->strKomentFilter);
	}
	
	function setType($_intType) {
		// Type of Koment;
		//the type should be  : 0 default, 2 receive pingback, 3 sent trackback, 4 receive trackback
		
		$this->intType = $_intType;
		
	}
	
	function getError() {
		return $this->errP->getError();
	}
	
	function setError($strErr_) {
		$this->errP->errors($strErr_);
		
	}
	
	function setMaxKomentperPage($maxKoment_) {
		$this->maxKomentperPage = $maxKoment_;
		
	}
	
	function readKoments(){


	
		$this->assignTemplate();
		
		// if maxpostperpage not set, then get it from default option in database
		if (!$this->maxKomentperPage)  		$this->maxKomentperPage = getOption("MaxKomentperPage");
		
		$DB = new Database($this->intDebug);
		
		$strSQL = "select k.*, j.strJudul from tblKoment k inner join tblJurnal j on j.IDJurnal = k.IDJurnal ";
	
		if ($this->intType<>'') {
			$this->addFilter("k.intType = " . $this->intType . "");
		}		
		
		$DB->setConn($conn);
		$DB->setstrSQL($strSQL);
		//batasi hanya sejumlah 'MaxposPerPage' yang tampil disatu halaman
		$DB->setLimit(0, $this->maxKomentperPage);
		$DB->setSort("k.IDKoment desc");
		//tampilkan halaman yang dimau.
		$DB->setPage($this->currentPage);
		//kalo ada filter, misalnya tampilin yang published (non draft)
		if (isset($this->strKomentFilter)) $DB->setFilter($this->strKomentFilter);
			
		if ($rs = $DB->retrieve()) {
		//kalau sukses membaca database
			$jumlahTotalKoments = $DB->getTotalRow();	
			
			if ($this->intDebug) $this->setError("<p>total posts: " . $jumlahTotalKoments . "</p>");
			//clear $isi untuk diisi
			unset($isi);
			
			while ($row = mysql_fetch_assoc($rs)){
				
				$isi .= $this->generateSingleKoment($row);					
			} // end while
			
			$this->jmlMaxPage = ceil($jumlahTotalKoments/ $this->maxKomentperPage);
			
			
		} else {
			$this->errP->errors("tidak bisa retrieve postingan");
			
		} // end if rs
	
		if ($this->intDebug) $this->errP->errors("<p>xx" . $DB->getstrSQL() . "</p>");  //debug
		
		unset($DB);
		return $isi;
	}
	
	function generateSingleKoment($row_) {
		//function to generate each single row
		
		$komentDate = getdate(strtotime($row_["dtmTanggal"]));
		
		$namaandURI = $row_["strKomentator"];
		if ($lURI = $row_["URIKomentator"]) {
			$namaandURI = "<a href=\"" . $lURI . "\">" . $namaandURI . "</a>";
		}
		
		$strKoment = replacelinebreak($row_["strKoment"]);

		$this->template->Assign("[ID]", $row_["IDKoment"]);
		$this->template->Assign("[PostKomented]", $row_["strJudul"]);
		$this->template->Assign("[Komentator]", $namaandURI);
		$this->template->Assign("[Koment]", $strKoment);
		
		$this->template->Assign("URIEdit" , BASEFOLDER . "/manage.php?IDk=" . $row_["IDKoment"] . "&menu=4"); 
		
		$tmpContent = $this->template->Parse("_koment_");
		
		return $tmpContent;
		
		
	}
	
	function generateNavigasi() {
	// function untuk membuat paging navigasi dari koment
		$paramPage = $this->currentPage;
		$jmlmaxpage = $this->jmlMaxPage;
		
		if ($this->intDebug) {
			$this->errP->errors("param page " . $paramPage);
			$this->errP->errors("jmlMaxpage " . $jmlmaxpage);
		}
		
		$tempquerystr = $_SERVER['REQUEST_URI'] . "&hal=";
		
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
}


class KomentForm extends Koment {
// this class is an extention from Koment.
// it render the komentform templalte.
// and if it need to read the existing koment, use the Koment class functionality

	var $strFormMode; // the value of this variable are : Insert, Edit
	var $IDKoment;
	
	function KomentForm($intDebug_) {
		$this->setDebug($intDebug_);
		$this->strTemplateName = "komentform.html" ;
		$this->assignTemplate();
	}
	
	

	
	function setIDKoment($intIDKoment_) {
		$this->IDKoment = $intIDKoment_;
		$this->setFormMode("Edit");
		// $this->addFilter("IDKoment = " . $intIDKoment_ . "");
	}
	
	function setFormMode($strFormMode_) {
		// the value of this variable are : Insert, Edit
		// Insert means, it's a blank form,
		// Edit means , it's read from the database to edit
		$this->strFormMode = $strFormMode_ ;
		
	}
	
	function generateForm() {
		if (!$rs = $this->retrieveKoment() ) {
			// if row failed tobe retrieve, then return false;
			return false;
		}
	

	if ($this->intDebug) echo "mode = " . $this->strFormMode;
	
		if ($this->strFormMode == "Insert") {
			$filetarget = "ngisikoment.php";
		} elseif ($this->strFormMode == "Edit") {

			/// ganti ini jadi ngeditkoment.php <- belum bikin

			$filetarget = "ngisikoment.php?mode=edit";
			$strKomentator = $rs["strKomentator"];
			$strEmail = $rs["emailKomentator"];
			$strURI = $rs["URIKomentator"];
			$strKoment = $rs["strKoment"];
		}
		
		$this->template->Assign("BaseFolder", BASEFOLDER );
		$this->template->Assign("FileTarget", $filetarget );
		$this->template->Assign("[ID]", $this->IDKoment );
		$this->template->Assign("[Komentator]", $strKomentator);
		$this->template->Assign("[Email]", $strEmail );
		$this->template->Assign("[URI]", $strURI );
		$this->template->Assign("[Koment]", $strKoment);
$this->template->Assign("[IDKategori]", $_SESSION['kat']);
		
		
		if ($generated = $this->template->Parse(_koment_)) {
			return $generated;
		} else {
			$this->setError("failed to parse the template while generating koment form[" . $this->IDKoment . "]");
			return false;
		}
		
	}

	function retrieveKoment(){
		
		$DBKoment = new Database($this->intDebug);
		
		$DBKoment->setConn($conn);
		$DBKoment->setstrSQL("Select * from tblKoment");
		$DBKoment->setFilter("IDKoment = " . $this->IDKoment . "");
		
		if ($row = $DBKoment->retrieve()) {
			if ($rs  = mysql_fetch_assoc($row)) {
				return $rs;
			} else {
				$this->setError("failed to retrieve koment : failed to fetch");
				return false;
			}
		
		} else {
			$this->setError("failed to retrieve koment");
			return false;
			
		}
		
	}	

}

?>
