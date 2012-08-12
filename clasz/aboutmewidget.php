<?php
include_once("widget.class.php");

class AboutMeWidget extends Widget {

	function AboutMeWidget($intDebugl=0) {
		$this->setDebug($intDebugl);
		$this->assignTemplate();
	}

	function retrieveWidget() {
		$links = "";
		$links .= "<ul>\n";
		$links .= "<li>";
		$links .= "
	  <a href=\"http://plus.google.com/101789409365440287007?rel=author\">Google+</a>\n";
		$links .= "</li>\n</ul>";
		

		//assign to template
		$this->template->Assign("[IsiWidget]", $links);
		
		//parsing
		if (!$tmpContent = $this->template->Parse("_widget_")) {
			if ($this->intDebug) $tmpContent = $this->template->getError();
		}
		
		return $tmpContent;	
	}

	function assignTemplate() {
		$theTemplate = getOption("widgettemplate");
		parent::assignTemplate("_widget_", $theTemplate);
	

	} // end function assigntemplate



}	
?>
