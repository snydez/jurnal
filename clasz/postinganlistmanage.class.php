<?php
include_once("postinglistwidget.class.php");

class PostingListManage extends PostListWidget {

	function setQuery() {
		$this->strSQL = "select * from tblJurnal";
	}

	function retrieveWidget() {
	
		$postingan = new Database($this->intDebug);
		
		$postingan->setstrSQL($strSQL);
		$postingan->setLimit(0,5);
		$postingan->setSort("IDJurnal desc");
		
		if ($rs=$postingan->retrieve()) {
			//if succcesfull retrieving rowset
			//assign Judul to  template 
			$this->parsingtemplate($rs);
	
		} else {
			$this->errW->errors("cannot retrieve wigdet -- postlist");
			if ($this->intDebug) $tmpContent = $DBw->getError();
		} //end if
		
		// if no problem then parsing the template into tmpContent then return it
		if (!$tmpContent = $this->template->Parse("_widget_")) {
			if ($this->intDebug) $tmpContent = $this->template->getError();
		}
		
		$tempp =  $postingan->getstrSQL() ;
		//$this->errW->errors("cannot retrieve wigdet -- postlist");
		// if ($this->intDebug) {
		
			// parent::errW->errors($tempp);
			// $temp = "<p>limit : " . $this->getLimit() . "</p>";
			// parent::errW->errors($tempp);
		// }
		
		unset($this->template);
		unset($postingan);
		
		return $tmpContent;

	}
	
	function parsingtemplate($rs) {
			//$this->template->Assign("[JudulWidget]", $this->getJudul());  //kalo untuk manage kaya;nya ga ada judul
						---- betuliinnnnn
			// read all links
			$posts .= "\n<ul>\n";
			while ($row=mysql_fetch_assoc($rs)) {
				$posts .= "<li>";
				$posts .= "<a href=\"" . BASEFOLDER . "/id/" . $row['IDJurnal'] . "\">";  
				$posts .= strip_tags($row['strJudul']);
				$posts .= "</a></li>\n";
			} // end while
			$posts .= "</ul>\n";
		
			//Assign allLinks to template
			$this->template->Assign("[IsiWidget]", $posts);
	}

}

?>