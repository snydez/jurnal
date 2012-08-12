<?php
include_once("database.class.php");
include_once("template.class.php");
include_once("errors.class.php");

class Sitemap {

	var $intDebug;
	var $errP;
	var $strTemplateName;
	var $strTemplateNameDetail;
	var $template;
	var $templatedetail;

	function Sitemap($intDebugl = 0) {
		$this->intDebug = $intDebugl;

		
	}

	function setTemplate($fulltemplatename) {
		$this->strTemplateName = $fulltemplatename;
		$this->strTemplateNameDetail = str_replace(".htm","detail.htm", $fulltemplatename);

	}

	function setDebug($intDebugl = 0) {
		$this->intDebug = $intDebugl;
	}

	function readPosts() {

		if (!$this->assignTemplate()) return false;
		
		$DB = new Database($this->intDebug);
		$strSQL = "select IDJurnal, strJudul, strJudulDeskripsi, strJurnal, dtmTanggal from tblJurnal";
	 	$DB->setstrSQL($strSQL);

		if ($rs = $DB->retrieve()) {
			//read sql
			while ($row = mysql_fetch_assoc($rs)) {
				if ($sitemapall = $this->generateSitemap($row)) {
					$sitemaps = $sitemaps . $sitemapall;
				} else {
					return false;
				}

			}

			

		} else {
			$this->errP->errors("tidak bisa retrieve postingan");
			
		} // end if rs

		if ($this->intDebug) $this->errP->errors("<p>xx" . $DB->getstrSQL() . "</p>");  //debug
		
		unset($DB);
		
		return $sitemaps;
	}

	function generateSitemap($rowl) {
		$URI = BASEFOLDER . "/id/" . $rowl['IDJurnal'] . "/" . JudulURI($rowl);

		$this->templatedetail->Assign("LINK",$URI);
		$this->templatedetail->Assign("LASTMOD", date('Y-m-d',strtotime($rowl["dtmTanggal"])));

		$this->templatedetail->Assign("PRIOR", "0.6");

		if ($generated = $this->templatedetail->Parse("_sitemapdetail_")) {
			return $generated;	
		} else {
			$this->setError("failed to parse the template while generating detail sitemap " . BASEFOLDER);
			return false;
		}
	
	}

	function generateSitemaps() {

		if ($sitemaps = $this->readPosts()) {
			$this->template->Assign("[URLSitemap]", $sitemaps);
			if ($allsitemaps = $this->template->Parse("_sitemap_")) {
				return $allsitemaps;
			} else {
				$this->setError("failed to parse the template while generating detail sitemap " . BASEFOLDER);
				return false;
			
			}
		}

	}

	function getError() {
		return $this->errP->getError();
	}
	
	function setError($strErr_) {
		$this->errP->errors($strErr_);
		
	}

	function assignTemplate() {

		
		// kalo belom set templatename nya, ambil dari default option database;
		if (!$this->strTemplateName) $this->strTemplateName = getOption("sitemaptemplate");
		if (!$this->strTemplateNameDetail) $this->strTemplateNameDetail = getOption("sitemaptemplatedetail");


				
		$this->template = new SimpleTemplate($this->intDebug);
		$this->templatedetail = new SimpleTemplate($this->intDebug);
		
		if (!$this->template->Define("_sitemap_", "" . TEMPLATEFOLDER . "/". $this->strTemplateName . "")) {
			$this->template->setError("Cannot set template" . $this->strTemplateName);
			return false;
			
		} else {
			return true;//end if
		}


		if (!$this->templatedetail->Define("_sitemapdetail_", "" . TEMPLATEFOLDER . "/". $this->strTemplateNameDetail . "")) {
			$this->templatedetail->setError("Cannot set template" . $this->strTemplateNameDetail);
			return false;
			
		} else {
			return true;//end if
		}





	} 

	





}


?>

