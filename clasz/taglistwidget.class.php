<?php
include_once("widget.class.php");

class TagListWidget extends Widget {

	function TagListWidget($intDebugl=0) {
		$this->setDebug($intDebugl);
		$this->assignTemplate();
		
	}
	
	function assignTemplate() {
		$theTemplate = getOption("widgettemplate");
		parent::assignTemplate("_widget_", $theTemplate);
	
	} // end function assigntemplate
	
	//???betulin database nya dulu !!!
	function retrieveWidget() {
		$DBt = new Database($this->intDebug);	

		$strSQL = "select IDTag as cat, count(*) as jml from relTag_Jurnal ";
		
		$DBt->setstrSQL($strSQL);
		$DBt->setSort("jml desc");
		$DBt->setGroup("IDTag");
		
		if ($rs = $DBt->retrieve()) {
			if ($DBt->getTotalRow()>0) {
				while ($row = mysql_fetch_assoc($rs)) {
					$tags[$row['cat']] = $row['jml'];
					
				} //end while
				
				$maxsize = 110; // %;
				$minsize = 80; //%;
				
				//get jml maksimum count dan jumlah mininum count
				$maxqty = max(array_values($tags));
				$minqty = min(array_values($tags));
				
				$spread = $maxqty - $minqty;
				//gak boleh nol, entar error kalo dibagi 0
				if ($spread == 0) $spread = 1;
		
				$step = ($maxsize - $minsize) / $spread;
				
				$tmptags = "\n<div class=\"tagcloud\">";
				$tmptags .= "\n<ul>\n";	
				foreach ($tags as $key=> $isijml) {
					$size = $minsize + (($isijml - minqty) * $step);
					if ($size>=85) {			
						$tmptags .= "<li><a href=\"" . BASEFOLDER ."/kat/" . $this->strKategori . "/tag/" . urlencode($key) . "\" style=\"font-size: " . $size . "%\" ref=\"tag\">";
						$tmptags .= $key;
						$tmptags .= "</a></li>\n";			
					} //selected > 100%
					
				} // for each
				$tmptags .= "</ul>\n";
				$tmptags .= "<p>more tags</p>\n";
				$tmptags .= "</div><!-- end tagcloud //-->\n";
				
				$this->template->Assign("[IsiWidget]", $tmptags);
				$this->template->Assign("[JudulWidget]", $this->getJudul());
				
				// if no problem then parsing the template into tmpContent then return it
					if (!$tmpContent = $this->template->Parse("_widget_")) {
					if ($this->intDebug) $tmpContent = $this->template->getError();
			
				}
			
				unset($this->template);
				unset($DBw);
			}
			return $tmpContent;
	
			
		} else {
			if ($this->intDebug) {
				return $DBt->getError();
			} else {
				return "Error";
			}
				
		} // end if rs
		}	//end function retrievewidget
	} //end class	

?>
