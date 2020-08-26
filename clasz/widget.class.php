<?php
include_once("database.class.php");
include_once("template.class.php");
include_once("errors.class.php");

class Widget {
	var $intDebug;
	var $errW;
	var $intWLimit;
	var $strJudulW;
	var $template;
	var $intLinkType;
	var $strKategori;
	
	function __construct($intDebugl = 0) {
		$this->intDebug = $intDebugl;
		$this->errW = new Errorc;
	}
	function setLimit($intLimit = 0) {
		if ($intLimit==0) {
			$this->intWLimit = getOption("MaxLink");
				
		} else {
			$this->intWLimit = $intLimit;
		}
		
	}
	
	function setJudul($strJudul = "") {
		if ($strJudul <> "") $this->strJudulW = $strJudul;
	}
	
	function setDebug($intDebugl) {
		$this->intDebug = $intDebugl;
	}
	
	function setLinkType($intTypel=0) {
		$this->intLinkType = $intTypel;
		// different type for each widget
	}


	function setKategori($strKat = 'Kat1') {
		// by defaul, set default kategori -> Kat1
		$this->strKategori = $strKat;
	}
	
	function getError() {
		return $this->errW->getError();
	
	}
	
	function getJudul() {
		if (!isset($this->strJudulW)) $this->setJudul("");
		return $this->strJudulW;
	}
	
	function getLimit() {
		if (!isset($this->intWLimit)) {
			$this->setLimit();
		} 		
		return $this->intWLimit;
	}
	
// tadinya function assignTemplate($definition, $theTemplate)
//tapi jadi ada notification error HP Strict Standards:  Declaration of XXX should be compatible with
//jadinya dikasih default empty value 
	 function wassignTemplate($definition='', $theTemplate='') {
		
		
		$this->template = new cSimpleTemplate();
		if (!$this->template->Define($definition, "" . TEMPLATEFOLDER . "/". $theTemplate . "")) {
			if ($this->intDebug) echo $this->template->getError();
		} //end if
		
	} // end function assigntemplate
	
	
} // end class

?>
