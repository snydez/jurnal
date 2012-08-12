<?php
include_once("widget.class.php");

class KomentListWidget extends Widget {
	var $template;
	var $intLinkType;

	function KomentListWidget($intDebugl=0) {
		$this->setDebug($intDebugl);
		$this->assignTemplate();
	}
	
	function retrieveWidget() {
		$koments = "";
		//the type should be  : 0 default, 2 receive pingback, 3 sent trackback, 4 receive trackback
		if (!isset($this->intLinkType)) $this->setLinkType(0);
		
		$DBkw = new Database($this->intDebug);
		$this->setLimit(7);
		$strSQL = "select * from tblKoment";
		
		$DBkw->setstrSQL($strSQL);
		$DBkw->setFilter("intType = " . $this->intLinkType . "");
		$DBkw->setLimit(0,$this->getLimit());
		$DBkw->setSort("IDKoment desc");
		
		
				
		if ($rs=$DBkw->retrieve()) {
			//if succcesfull retrieving rowset
			//assign Judul to  template 
			
			$this->template->Assign("[JudulWidget]", $this->getJudul());
			
			//read koments
			$koments .= "\n<ul>\n";
			while ($row = mysql_fetch_assoc($rs)){
				$koments .= "<li>";
				$koments .= "<a href=\"".BASEFOLDER . "/komen/" . $row['IDJurnal'] . "#" . $row['IDKoment']  . "\" >";
				$koments .= $row['strKomentator'] . "</a>:&nbsp&nbsp;";
		
				//displaying only 11 kata!
				$koments .=  $this->limitto($row['strKoment'],11) . "";
				$koments .= "</li>\n";
			} // end while
			$koments .= "\n</ul>\n";
			
			//Assign allLinks to template
			$this->template->Assign("[IsiWidget]", $koments);
		} else {
			if ($this->intDebug) $tmpContent =  $DBw->getError();
			
		} //end if
		
		// if no problem then parsing the template into tmpContent then return it
		if (!$tmpContent = $this->template->Parse("_widget_")) {
			if ($this->intDebug) $tmpContent = $this->template->getError();
		}
		
		if ($this->intDebug) {
			echo "<P>strSQL : " . $DBkw->getstrSQL() . "</p>";
			echo "<p>limit : " . $this->getLimit() . "</p>";
		}
		
		unset($this->template);
		unset($DBkw);
		
		return $tmpContent;
		
	}
	
		
	
	function assignTemplate() {
		$theTemplate = getOption("widgettemplate");
		parent::assignTemplate("_widget_", $theTemplate);
	

	} // end function assigntemplate
	
	function limitto($koment, $numberx) {
	    // membatasi tampilan, hanya beberapa [numberx] kata saja dari total keseluruhan kata pada [koment]
		// provate function
		
		$koment= strip_tags($koment);
		$nkata = split(" ",$koment);
		
		for ($i=0;$i<$numberx;$i++) {
			$ngabung[$i] = $nkata[$i];
		}

		return join(" ",$ngabung) . "[~~]";
	}


} //end class
?>