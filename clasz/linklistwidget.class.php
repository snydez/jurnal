<?php
include_once("widget.class.php");

class LinkListWidget extends Widget {
	

	
	function __construct($intDebugl=0) {
		parent::__construct();
		$this->setDebug($intDebugl);
		$this->assignTemplate();
	}
	
		
	function retrieveWidget() {
		$links = "";
		
		// 0 type is Default link
		//the type should be  : 0 : default, 7 : concience, 8 : inactive, 9 : Dead
		if (!isset($this->intLinkType)) $this->setLinkType(0);
				
		$DBw = new Database($this->intDebug);
		
		$strSQL = "select * from tblLinks ";
		
		$DBw->setConn($conn);
		$DBw->setstrSQL($strSQL);
		$DBw->setFilter("intType = " . $this->intLinkType . "");
		$DBw->setLimit(0, $this->getLimit()); //ambil limit yang udah diset. atau yang default
		
		
		if ($rs=$DBw->retrieve()) {
		
			//if succcesfull retrieving rowset
			//assign Judul to  template 
			
			$this->template->Assign("[JudulWidget]", $this->getJudul());
						
			// read all links
			$links .= "<ul>\n";
			while ($row= $rs -> fetch_assoc()) {
				$links .= "<li>";
				$links .= "<a href=\"" . $row['URI'] . "\" target=\"_blank\" name=\"". $row['strDescr'] ."\">";
				$links .= $row['strBloggerName'];
				$links .= "</a></li>\n";
			} // end while
			$links .= "</ul>\n";
		
			//Assign allLinks to template
			$this->template->Assign("[IsiWidget]", $links);
		} else {
			if ($this->intDebug) $tmpContent = $DBw->getError();
		} //end if
		
		// if no problem then parsing the template into tmpContent then return it
		if (!$tmpContent = $this->template->Parse("_widget_")) {
			if ($this->intDebug) $tmpContent = $this->template->getError();
		}
		
		if ($this->intDebug) {
			echo "<P>strSQL : " . $DBw->getstrSQL() . "</p>";
			echo "<p>limit : " . $this->getLimit() . "</p>";
		}
		
		unset($this->template);
		unset($DBw);
		
		return $tmpContent;

	}
	
	private function assignTemplate() {
		$theTemplate = getOption("widgettemplate");
		parent::wassignTemplate("_widget_", $theTemplate);
	

	} // end function assigntemplate
	
	
}

?>
