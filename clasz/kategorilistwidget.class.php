<?php
include_once("widget.class.php");

class KategoriListWidget extends Widget {
	

	
	function KategoriListWidget($intDebugl=0) {
		$this->setDebug($intDebugl);
		$this->assignTemplate();
	}
	
		
	function retrieveWidget() {
		$links = "";
		
				
		$DBw = new Database($this->intDebug);
		
		$strSQL = "select * from tblKategori ";
		$DBw->setstrSQL($strSQL);
		$DBw->setLimit(0, $this->getLimit()); //ambil limit yang udah diset. atau yang default
		
		
		if ($rs=$DBw->retrieve()) {
		
			//if succcesfull retrieving rowset
						
			// read all links
			$links .= "<ul>\n";
			while ($row=mysql_fetch_assoc($rs)) {
				$links .= "<li>";
				$links .= "<a href=\"" . BASEFOLDER . "/kat/" .  $row['IDKategori'] . "\"  name=\"". $row['IDKategori'] ."\">";
				$links .= $row['strKategori'];
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
	
	function assignTemplate() {
		$theTemplate = getOption("topwidget");
		parent::assignTemplate("_widget_", $theTemplate);
	

	} // end function assigntemplate
	
	
}

?>