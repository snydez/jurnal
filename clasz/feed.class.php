<?php
include_once("database.class.php");
class Feed {
	var $variabel = array();
	var $strFeedAll = "";
	var $strFeedType = "rss20"; /* feedtype = rss, atom, rss20 */
	var $strFeedOf = "jurnal"; /* feedof = jurnal, koment[ID] */
	var $intDebug;
	
	function __construct($intDebugl = 0) {
		
		$this->intDebug = $intDebugl;
		
		$lastbuild = new Database($intDebug);
		
		$lastbuild->setConn($conn);
		$lastbuild->setstrSQL("select max(coalesce(dtmModify,dtmTanggal)) as maxmod from tblJurnal");
		if ($hasil=$lastbuild->retrieve()) {
			$row = mysql_fetch_assoc($hasil);
			$this->variabel["datebuild"] = strtotime($row["maxmod"]);
		} else {
			$this->variabel["datebuild"] = strtotime("now");
		}
		$lastbuild = null;
		unset($lastbuild);
	}
	
	function sets($variablename, $variablevalue) {
		$this->variabel[$variablename] = $variablevalue;
	}
	
	function setkomentID($strxfeedof) {
		$this->strFeedOf = $strxfeedof;
	}
	
	function setfeedtype($strxfeedtype) {
		$this->strFeedType = $strxfeedtype;
	}
	
	function display() {
		$dbjurnal = new Database;
		
		
		$dbjurnal->setConn($conn);
		if ($this->strFeedOf=="jurnal") {
		// kalao mau burn jurnal
			$strSQL = "select *, coalesce(dtmModify,dtmTanggal) as TglifNull from tblJurnal ";
	
			$dbjurnal->setstrSQL($strSQL);
			$dbjurnal->setFilter("bolDraft <> 1");
			$dbjurnal->setSort("TglifNull desc");
			$dbjurnal->setLimit(0,10);
			
			$this->variabel["feedtitle"]="the Days After";
			$this->variabel["feedlink"] ="http://jurnal.snydez.com/";
			$this->variabel["feeddescription"]="Snydez Personal Journal";
			
			$this->variabel["komentAPI"]=" xmlns:wfw=\"http://wellformedweb.org/CommentAPI/\" ";
		} else {
			// kalo mau burn koment		
			$strSQL = "select * from tblKoment ";
			
			$dbjurnal->setstrSQL($strSQL);
			$dbjurnal->setFilter("IDJurnal = " . $this->strFeedOf);
			$dbjurnal->setSort("IDKoment desc");
			
			$this->variabel["feedtitle"]="the Days After, the Koment";
			$this->variabel["feedlink"] ="http://jurnal.snydez.com/lastkoment.php";
			$this->variabel["feeddescription"]="comments of Snydez Personal Journal";
		}
		if (!($this->strFeedType)) $this->strFeedType="rss20";		
		//set up header, according feedtype
		
		$this->strFeedAll = $this->getheader();
		
		if ($hasil = $dbjurnal->retrieve() ) {
		
			while ($row=mysql_fetch_assoc($hasil)) {
				$this->strFeedAll .= $this->getbody($row);
			}
			
		} else {
			echo $dbjurnal->getError();
			return false;
		}
		
		if ($this->intDebug) echo $dbjurnal->getError();
		
		$this->strFeedAll .= $this->getfooter();
		
		$dbjurnal = null;
		unset($dbjurnal);
		return $this->strFeedAll;
		
	}
	
	function gets($variablename, $tag) {
		if (isset($this->variabel[$variablename])) {
			return "<$tag>" . $this->variabel[$variablename] . "</$tag>\n";
		} else {
			return "";
		}
	}
	
	function getheader() {
		$tmpstr = "";
		if ($this->strFeedType=="rss") {
		
			$tglbuild =  date("D, d M Y H:i:s +0700",$this->variabel["datebuild"]);
			$yearbuild = date("Y", $this->variabel["datebuild"]);
			
			$tmpstr .= "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>\n";
			$tmpstr .= "<rss version=\"0.92\">\n";
			$tmpstr .= "<channel>\n";
			$tmpstr .= $this->gets("feedtitle","title"); 
			$tmpstr .= $this->gets("feedlink","link");
			$tmpstr .= $this->gets("feeddescription","description");
			
			$tmpstr .= "  <lastBuildDate>". $tglbuild . "</lastBuildDate>\n";
			$tmpstr .= "  <docs>http://backend.userland.com/rss092</docs>\n";
			$tmpstr .= "  <managingEditor>mail@snydez.com</managingEditor>\n";
			$tmpstr .= "  <webMaster>mail@snydez.com</webMaster>\n";
			$tmpstr .= "  <language>en-us</language>\n";
			$tmpstr .= "  <language>id</language>\n";
			$tmpstr .= "  <copyright>Copyright (c) 2002 - ". $yearbuild ." snydez.com</copyright>\n";
		} elseif ($this->strFeedType=="atom") {
			
			$tglbuild = date("Y-m-d\TH:i:s\Z", $this->variabel["datebuild"]);
			$yearbuild = date("Y", $this->variabel["datebuild"]);
			
			$tmpstr .=  "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
			$tmpstr .=  "<feed xmlns=\"http://www.w3.org/2005/Atom\" xmlns:thr=\"http://purl.org/syndication/thread/1.0\"   xml:lang=\"en\"   xml:base=\"http://jurnal.snydez.com/xml/index.php?rsstype=atom\" >\n";
			$tmpstr .= $this->gets("feedtitle","title"); 
			$tmpstr .=  "<link rel=\"alternate\" type=\"text/html\" href=\"" . $this->variabel["feedlink"] . "\" />\n";
			$tmpstr .=  " <updated>" . $tglbuild . "</updated>\n";
			$tmpstr .=  " <subtitle>" . $this->variabel["feeddescription"] . "</subtitle>\n";
			$tmpstr .=  " <id>tag:snydez.com,2005:jurnal</id>\n";
			$tmpstr .=  " <generator uri=\"http://snydez.com/\" version=\"0.8\">snydez.com rssXML</generator>\n";
			$tmpstr .=  " <link rel=\"self\" type=\"application/atom+xml\" href=\"http://jurnal.snydez.com/feed/atom\"/>\n";
			$tmpstr .=  " <rights>Copyright (c) 2002 - ". $yearbuild ." snydez.com</rights>\n";
		} elseif ($this->strFeedType=="rss20") {
			$tglbuild =  date("D, d M Y H:i:s +0700",$this->variabel["datebuild"]);
			$yearbuild = date("Y", $this->variabel["datebuild"]);
			
			$tmpstr .=  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$tmpstr .=  "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" ". $this->variabel["komentAPI"] ." xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
			$tmpstr .=  "<channel>\n";
			$tmpstr .= $this->gets("feedtitle","title"); 
			$tmpstr .= $this->gets("feedlink","link");
			$tmpstr .= $this->gets("feeddescription","description");
			$tmpstr .=  "<pubDate>" . $tglbuild . "</pubDate>\n";
			$tmpstr .=  "<generator>snydez.com rssXML</generator>\n";
			$tmpstr .=  "<language>en</language>\n";
			$tmpstr .= "  <copyright>Copyright (c) 2002 - ". $yearbuild ." snydez.com</copyright>\n";
		}
		
		return $tmpstr;
		
	}
	
	function initiatebody($xrow) {
		$find = array("<p>","<br/>", "<br>","</p>","\'");
		$replace = array(chr(13).chr(10),chr(13).chr(10),chr(13).chr(10) ,"","&8217;");
	
		$strJudulDeskripsi = $xrow["strJudulDeskripsi"];
	
		if ($this->strFeedOf=="jurnal") {
		
		$strJurnal = $xrow["strJurnal"];
		$strJurnal= sensor($strJurnal);
			
			$strID = $xrow["IDJurnal"];
			
			
			
			if ($xrow["strJudul"]!="") {
				$strJudul = strip_tags($xrow["strJudul"]); 
			} else {
				//kalau ga ada judul, ambil sekian karakter dari postingan yang udah di strip tagnya
				$strJudul = substr(strip_tags($strJurnal),0,20) . "..";
			} // end judull
			
			if ($xrow["strDeskripsi"]!="") {
				$strDeskripsi = ($xrow["strDeskripsi"]);

				$strDeskripsi = sensor($strDeskripsi);
			} else {
				//kalau gak ada deskripsi, ambil sebagian karakter dari postingan.
				$strDeskripsi = substr(strip_tags(sensor($strJurnal)),0,195) . "..";
			}
		
			
			
			
			$this->variabel["ID"] = $strID;
			
			$this->variabel["bodyjudul"] = $strJudul;
			$this->variabel["bodydescriptiontext"]= strip_tags(str_replace($find,$replace,$strDeskripsi));
			$this->variabel["bodydescriptionencode"]= htmlspecialchars($strDeskripsi);
			$this->variabel["bodydescriptionhtml"]= hardhostlink($strDeskripsi);
			
			$tglpost = strtotime($xrow["dtmTanggal"]);
			$this->variabel["bodydatepostrss"] = date("D, d M Y H:i:s +0700",$tglpost);
			$this->variabel["bodydatepostatom"] = date("Y-m-d\TH:i:s\+07:00",$tglpost);
			
			if ($xrow["dtmModify"]=="") {
				$tglmodify=$tglpost; 
			} else {
				$tglmodify = strtotime($xrow["dtmModify"]);
			}
									
			$this->variabel["bodydatemodifyrss"] = date("D, d M Y H:i:s +0700",$tglmodify);
			$this->variabel["bodydatemodifyatom"] = date("Y-m-d\TH:i:s\+07:00",$tglmodify);
			
			$this->variabel["bodypermalink"] = "http://jurnal.snydez.com/id/" . $strID . "/" . urlencode($strJudulDeskripsi) ;
			$this->variabel["bodyauthor"] = "snydez";
			
		
		} else {
		// koment system
			$strID = $xrow["IDKoment"];
			$strPostID = $xrow["IDJurnal"];
			
			$this->variabel["ID"] = $strID;
			$this->variabel["PostID"] = $strPostID;
			
			$this->variabel["bodyjudul"] = "koment " . ($xrow["strKomentator"]);
			$this->variabel["bodydescriptiontext"]= str_replace($find,$replace,$xrow["strKoment"] );
			$this->variabel["bodydescriptionencode"]= htmlspecialchars($xrow["strKoment"]);
			$this->variabel["bodydescriptionhtml"]= $xrow["strKoment"];
			
			$tglpost = strtotime($xrow["dtmTanggal"]);
			$this->variabel["bodydatepostrss"] = date("D, d M Y H:i:s +0700",$tglpost);
			$this->variabel["bodydatepostatom"] = date("Y-m-d\TH:i:s\+07:00",$tglpost);
						
			$this->variabel["bodydatemodifyrss"] = $this->variabel["bodydatepostrss"];
			$this->variabel["bodydatemodifyatom"] = $this->variabel["bodydatepostatom"];
			
			$this->variabel["bodypermalink"] = "http://jurnal.snydez.com/komen/" . $strPostID . "/#koment-" . $strID  ;
			$this->variabel["bodyauthor"] = ($xrow["strNama"]);
			
		
		}
	
	}
	
	function hanyakarakter($karakter) {
		$regexsearch = "[^A-Za-z0-9]";
		$regexreplace = "";
						  
	  return  ereg_replace($regexsearch, $regexreplace, $karakter);
	
	}
	
	function getbody($xrow) {
		$tmpstr = "";
		
		$this->initiatebody($xrow);
		$strID = 	$this->variabel["ID"]; 
		
		if ($this->strFeedType=="rss") {
		
			if ($xrow["strDeskripsi"]!="") {
				$strJurnal = strip_tags($xrow["strDeskripsi"]);
			} else {
				$strJurnal = substr(strip_tags(sensor($strJurnal)),0,195) . "..";
			}
			$tglpost = date("D, d M Y H:i:s +0700",$tglpost);
		
			//bikin rss0.92
			$tmpstr .= "<item>\n";
			$tmpstr .= "\t<title>" . $this->variabel["bodyjudul"] . "</title>\n";
			$tmpstr .= "\t<description>\n\t\t" . $this->variabel["bodydescriptionencode"] . "\n\t</description>\n";
			//$tmpstr .= "\t<datepost>" . $this->variabel["bodydatepostrss"] . "</datepost>\n";
			$tmpstr .= "\t<link>" .$this->variabel["bodypermalink"] . "</link>\n";
			$tmpstr .= "</item>\n";
			
			// end bikin rss0.92
		} elseif ($this->strFeedType=="atom") {
			//bikin atom
						
			$tmpstr .= "<entry>\n";
			$tmpstr .= "\t<title>" . $this->variabel["bodyjudul"] . "</title>\n";
			$tmpstr .= "\t<link rel=\"alternate\" type=\"text/html\" href=\"" . $this->variabel["bodypermalink"]  . "\" />\n";
			$tmpstr .= "\t<author>\n";
			$tmpstr .= "\t\t<name>" .$this->variabel["bodyauthor"].  "</name>\n";
			if ($this->strFeedOf=="jurnal") {
				$tmpstr .= "\t\t<uri>http://jurnal.snydez.com</uri>\n";
				$tmpstr .= "\t\t<email>milis@-removethiz-snydez.com</email>\n";
			}
			$tmpstr .= "\t</author>\n";
			$tmpstr .= "\t<published>" .$this->variabel["bodydatepostatom"]. "</published>\n";
			$tmpstr .= "\t<id>tag:snydez.com,2005:jurnal.post-". $this->variabel["ID"]. "</id>\n";
			$tmpstr .= "\t<updated>" .$this->variabel["bodydatemodifyatom"]. "</updated>\n";
			
			//tulis kateogri
			$cattemplate = "<category scheme=\"http://jurnal.snydez.com\" term=\"#CAT#\" />";
			$atomCat = $this->ambilcategory($strID,$cattemplate);
			
			if ($atomCat) 		{$tmpstr .= $atomCat;}
			
			
			$tmpstr .= "\t<summary type=\"html\">\n";
			$tmpstr .= "\t\t<![CDATA[" . $this->variabel["bodydescriptionhtml"] . "]]>\n";
			$tmpstr .= "\t</summary>\n";
			$tmpstr .= "</entry>\n";
			
		// end bikin atom
		} elseif  ($this->strFeedType=="rss20") {
					
			$tmpstr .= "<item>\n";
			$tmpstr .= "\t<title>" . $this->variabel["bodyjudul"] . "</title>\n";
			$tmpstr .= "\t<link>" .$this->variabel["bodypermalink"] . "</link>\n";
			if ($this->strFeedOf=="jurnal") 
				$tmpstr .= "\t<comments>http://jurnal.snydez.com/koment/" . $this->variabel["ID"] . "</comments>\n";
			$tmpstr .=  "\t<pubDate>" . $this->variabel["bodydatepostrss"] . "</pubDate>\n";
			if ($this->strFeedOf=="jurnal") {
				$tmpstr .=  "\t<dc:creator>" . $this->hanyakarakter($this->variabel["bodyauthor"]). "</dc:creator>\n";
			} else {
				$tmpstr .=  "\t<author>" . $this->hanyakarakter($this->variabel["bodyauthor"]) . "@noemail.com</author>\n";
			}
			//tulis kateogri
				$atomCat .= $this->ambilcategory($strID,"<category>#CAT#</category>");
			if ($atomCat) 	{	$tmpstr .= $atomCat;}
			$tmpstr .= "\t<guid isPermaLink=\"false\">" .$this->variabel["bodypermalink"]. "</guid>\n";
			$tmpstr .= "\t<description>\n";
			$tmpstr .= "\t\t<![CDATA[" . $this->variabel["bodydescriptionhtml"] . "]]>\n\t</description>\n";		
			$tmpstr .= "\t<content:encoded>\n";
			$tmpstr .= "\t\t<![CDATA[" . $this->variabel["bodydescriptionhtml"]  . "]]>\n\t</content:encoded>\n";
			if ($this->strFeedOf=="jurnal") 
				$tmpstr .= "\t<wfw:commentRss>http://jurnal.snydez.com/feed/koment/" . $this->variabel["ID"] . "/" . $this->strFeedType . "</wfw:commentRss>\n";
			
			$tmpstr .= "</item>\n"; 
		
		}
		
		
		return $tmpstr;
			
	}
	
	function getfooter() {
		$tmpstr = "";
	
		if ($this->strFeedType=="rss" || $this->strFeedType=="rss20" ) {
			$tmpstr .= "</channel>\n";
			$tmpstr .= "</rss>";
		} elseif ($this->strFeedType=="atom") {
			$tmpstr .= "</feed>";
		} 
	
		return $tmpstr;
	}
	function gantianeh($strIncoming) {
		$regsearch = array("'&'",
						   "'\''",
						   "'\"'",
						   "'<sensor>.*?</sensor>'",
						   "'<script.*?</script>'si"
						   ); 
		$regreplace = array("&amp;",
							 "&#8217;",
							 "&quot;",
							 "<IMG SRC=\"$thisserver/img/sensor.gif\">",
							 "");
							 
		$strGanti = preg_replace ($regsearch, $regreplace, $strIncoming);
	
	return $strGanti;
	}
	
	function ambilcategory($LINK_ID,$strTag) {
		$resultz = '';
		$dbCategory = new Database;
		
		$dbCategory->setConn($conn);
		
		$dbCategory->setstrSQL("select * from relTag_Jurnal");
		$dbCategory->setFilter("IDJurnal =" .  $LINK_ID  );
		$dbCategory->setSort("IDTag");
		
		if ($hasil = $dbCategory->retrieve()) {
		
			$dbCategory = null;
			unset ($dbCategory);
			
			while ($rowc = mysql_fetch_assoc($hasil)) {
				$resultz .= str_replace("#CAT#", $rowc["IDTag"], $strTag) . "\n";
			}
			return $resultz;
		} else {
			return false;
		}
		
	}
	
}
?>
