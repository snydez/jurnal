<?php
include_once("widget.class.php");

class AboutMeWidget extends Widget {

	function AboutMeWidget($intDebugl=0) {
		$this->setDebug($intDebugl);

		$strJudul  = "<a href=\"http://about.me/snydez\" target=\"_blank\">about.me</a>";
		parent::setJudul($strJudul);
		$this->assignTemplate();
	}

	function retrieveWidget() {
		$links = "";
		$links .= "<ul>\n";
		$links .= "<li>";
		$links .= "
	  <a href=\"http://plus.google.com/101789409365440287007?rel=author\" target=\"_blank\">Google+</a>\n";
		$links .= "</li>";

		$links .= "<li>";
		$links .= "<a href=\"https://twitter.com/snydez\" class=\"twitter-follow-button\" data-show-count=\"false\" data-lang=\"en\" target=\"_blank\">@snydez</a>";
		$links .= "</li>\n";


		$links .= "\n</ul>";
		


		

		//assign to template
		$this->template->Assign("[IsiWidget]", $links);
		$this->template->Assign("[JudulWidget]", $this->getJudul());
		
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
