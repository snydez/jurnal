<?php
include_once("widget.class.php");

class AdsenseWidget extends Widget {

	function AdsenseWidget($intDebugl=1) {
		$this->setDebug($intDebugl);
		$this->assignTemplate();
	}

	function retrieveWidget() {
		

		$thecode = 
"<script type=\"text/javascript\"><!--
google_ad_client = \"ca-pub-2405329547214743\";
/* techsnydez */
google_ad_slot = \"0019111551\";
google_ad_width = 234;
google_ad_height = 60;
//-->
</script>
<script type=\"text/javascript\"
src=\"http://pagead2.googlesyndication.com/pagead/show_ads.js\">
</script>";

		//assign to template
		$this->template->Assign("[IsiWidget]", $thecode);
		$this->template->Assign("[JudulWidget]", "ad");
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
