<?php

include_once("template.class.php");
include_once("errors.class.php");

class TwitterCard {
	var $strTemplateName;
	var $TitleTC;
	var $DescriptionTC;
	var $ImageTC;
	var $PermalinkTC;
	var $template;
	

	function __construct($intDebugl=0){

 	    
	}

	function setTemplate($fulltemplatename) {
		$this->strTemplateName = $fulltemplatename;
	}

	function assignTemplate() {


	
		// kalo belom set templatename nya, ambil dari default option database;
		if (!$this->strTemplateName) $this->strTemplateName = "twittercard.html";
				
		$this->template = new cSimpleTemplate;
		
		if (!$this->template->Define("_twittercard_", "" . TEMPLATEFOLDER . "/". $this->strTemplateName . "")) {
			$this->template->setError("Cannot set template" . $this->strTemplateName);
			return false;
			
		} //end if

	} 

	function setCard($cardname,$cardvalue) {
			
		$this->template->Assign($cardname, $cardvalue); 
	}

	function loadCard(){
		
		return $this->template->Parse("_twittercard_");
	}

	



}
?>
