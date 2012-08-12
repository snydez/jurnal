<?php
include_once("widget.class.php");

class SearchBoxWidget extends Widget {
	var $template;
	var $intLinkType;
	var $paramSearch;
	var $urlTargetAction;
	
	function SearchBoxWidget($intDebugl = 0) {
		$this->setDebug($intDebugl);
		$this->assignTemplate();
		
	}

	function retrieveWidget() {
	
		$thetext = "<div class=\"searchbox\">\n";
		$thetext .= "<form method=\"GET\" action=\"" . $this->urlTargetAction .  "\">\n";
		
		$thetext .= "<input type=\"text\" name=\"cari\" id=\"cari\" ";
		if ($this->paramSearch) {
			$thetext .= "value=\"" . $this->paramSearch . "\" ";
		}
		$thetext .= "><input type=\"submit\" name=\"btnGo\" value=\"Go\" >\n";
		if ($this->paramSearch) {
			$thetext .= "<input type=\"submit\" name=\"btnClear\" value=\"clear\">\n";
		}
		$thetext .= "</form>\n</div>";
		
		
		$this->template->Assign("[JudulWidget]",$this->getJudul());
		
		$this->template->Assign("[IsiWidget]", $thetext);
		
		if(!$tmpContent = $this->template->Parse("_widget_")) {
			if ($this->intDebug) $tmpContent = $this->template->getError();
		}
		
		if ($this->intDebug) {
			echo "<div class=\"error\">Param Search : " . $this->paramSearch . "<br/> Judul : ". $this->getJudul() . "</div>";
		}
		
		
		
		unset($this->template);
		
		return $tmpContent;
	}
	
	function setParamSearch($paraml) {
		$this->paramSearch = $paraml;
	}
	
	function setTarget($targetl) {
		$this->urlTargetAction = $targetl;
	}
	
	function assignTemplate() {
		$theTemplate = getOption("widgettemplate");
		parent::assignTemplate("_widget_", $theTemplate);
	

	} // end function assigntemplate
}