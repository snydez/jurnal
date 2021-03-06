<?php
include_once("widget.class.php");

class PostListWidget extends Widget {

	var $strSQL;
	var $IDparaml;
	
	function __construct($intDebugl=0) {
		parent::__construct();
		$this->setDebug($intDebugl);
		$this->setQuery();
		$this->assignTemplate();
		
	}
	
	
	function setQuery() {
		$this->strSQL = "select IDJurnal, strJudul, strJudulDeskripsi, strJurnal from tblJurnal j , tblKategori k";
	}
	
	function parsingtemplate($rs) {
			$this->template->Assign("[JudulWidget]", $this->getJudul());
						
			// read all links
			$posts .= "\n<ul>\n";
			while ($row= $rs -> fetch_assoc()) {
				$posts .= "<li>";
				$posts .= "<a href=\"" . BASEFOLDER . "/id/" . $row['IDJurnal'] . "/" . JudulURI($row) . "\">";  
				$posts .= strip_tags($row['strJudul']);
				$posts .= "</a></li>\n";
			} // end while
			$posts .= "</ul>\n";
		
			//Assign allLinks to template
			$this->template->Assign("[IsiWidget]", $posts);
	}
	
	function retrieveWidget() {
		$posts = "";
		
		//the type should be  : 0 default, 1 posting micro
		if (!isset($this->intLinkType)) $this->setLinkType(0);
				
		$DBw = new Database($this->intDebug);
		
		$DBw->setConn($conn);
		$DBw->setstrSQL($this->strSQL);
		// if specifiedID exist, grab $this->getLimit() ID posting 
		
		if (isset($this->IDparaml)) $strFilterl = " and (IDJurnal < " . ($this->IDparaml + $this->getLimit()) . ")";
		
		
		$DBw->setFilter("intType = " . $this->intLinkType . " AND strJudul<>'' AND bolDraft = false " . $strFilterl . " AND j.IDKategori = k.IDKategori AND k.IDKategori = '" . $this->strKategori ."'" );
		$DBw->setLimit(0, $this->getLimit()); //ambil limit yang udah diset. atau yang default
		$DBw->setSort("IDJurnal desc");
		
		
		if ($rs=$DBw->retrieve()) {
		
			//if succcesfull retrieving rowset
			//assign Judul to  template 
			$this->parsingtemplate($rs);
	
		} else {
// snydez remark 20200115		
//			$this->errW->errors("cannot retrieve wigdet -- postlist");
			if ($this->intDebug) $tmpContent = $DBw->getError();
		} //end if
		
		// if no problem then parsing the template into tmpContent then return it
		if (!$tmpContent = $this->template->Parse("_widget_")) {
			if ($this->intDebug) $tmpContent = $this->template->getError();
		}
		
		$tempp =  $DBw->getstrSQL() ;
		//$this->errW->errors("cannot retrieve wigdet -- postlist");
		// if ($this->intDebug) {
		
			// parent::errW->errors($tempp);
			// $temp = "<p>limit : " . $this->getLimit() . "</p>";
			// parent::errW->errors($tempp);
		// }
		
		unset($this->template);
		unset($DBw);
		
		return $tmpContent;

	}
	
	
	function setSpecificID($IDspecific) {
	    
		if (isset($IDspecific)) $this->IDparaml = $IDspecific;
		//if ($this->intDebug) $this->errP->errors("ID: " . $IDspecific);
	}
	
	private function assignTemplate() {
		$theTemplate = getOption("widgettemplate");
		parent::wassignTemplate("_widget_", $theTemplate);
	

	} // end function assigntemplate
} //end class
?>
