<?php
/* Class ini dipergunakan untuk mengoperasikan database secara umum
   author : sonny dezerial mail@snydez.com
   last modification : 3:30 PM Tuesday, August 07, 2007
*/

include_once("dbconnx.php");
include_once("errors.class.php");

class Database  {
	var $strSQL;
	
	var $intAwal;
	var $intLimit;
	var $sortascdesc;
	var $intTotalRow;
	var $strGroupBy;
	var $strFilter;
	var $errc;
	var $intDebug;
	

	function Database($intDebugl=0) {
		$this->errc = new Errorc;
		$this->setDebug($intDebugl);
	}
	
	function setstrSQL($strxSQL) {
		$this->strSQL = $strxSQL;
	}
	
	function setSort($xwhatfieldwhatsort) {
		$this->sortascdesc = $xwhatfieldwhatsort;
	}
	
	function retrieve() {

		
		$strxSQLFilter = "";
		if (isset($this->strFilter)) {
			$strxSQLFilter = " and " . $this->strFilter ."";
		}
				
		$strxSQLOrder = "";
		if (isset($this->sortascdesc)) {
			$strxSQLOrder = " order by " . $this->sortascdesc;
		}
		

		$strxSQLLimit = "";
		if ($this->intLimit>0) {
			if (!isset($this->intAwal)) $this->intAwal = 0;
			$strxSQLLimit = " limit " . $this->intAwal . ", " . $this->intLimit;
		}
		
		$strxGroup = "";
		if (isset($this->strGroupBy)) {
			$strxGroup = " GROUP BY " . $this->strGroupBy;
		}

		if (isset($this->strSQL)) {
			// masukkan parameter jumlah row
			$this->setTotalRow($this->strSQL . " where 1=1  " . $strxSQLFilter. $strxGroup);
			
			$this->strSQL = $this->strSQL . " where 1=1  " . $strxSQLFilter . $strxGroup . $strxSQLOrder . $strxSQLLimit .";" ;

			$xhasil = mysql_query($this->strSQL);
			if ($xhasil) {
				return $xhasil;
			} else {
				$this->errc->errors("Tidak bisa membaca data -- query -- retrieve -- database");
				return false;
			}
		
		} else {
			$this->errc->errors("SQLstring tidak ada --  -- retrieve -- database");
			return false;
		}
		
	}
	
	function update() {
		if (isset($this->strSQL)) {
			// check apakah sql string nya = "update"
			if (substr($this->strSQL,0,6)=="update") {
				$xhasil = mysql_query($this->strSQL) ;
				if ($xhasil) {
					return true;
				}	else  {
					$this->errc->errors("Tidak bisa mengupdate data -- query -- update -- database");
					if ($this->intDebug) $this->errc->errors($this->strSQL);
					return false;
				}
			} else {
				$this->errc->errors("Tidak bisa update date, salah SQLstring -- -- update -- database");
				return false;
			}
		} else {
			$this->errc->errors("Tidak ada sqlstring -- -- update -- database");
			return false;
		}
	
	}
	
	function create() {
		
		if (isset($this->strSQL)) {
			// check apakah SQL string nya bener "insert bla bla" apa engga.
			if (substr($this->strSQL,0,6)=="insert") {
				$xhasil = mysql_query($this->strSQL);
				if ($xhasil) {
					return true;
				} else {
					$this->errc->errors("Tidak bisa menulis data -- query -- create -- database");
					if ($this->intDebug) $this->errc->errors($this->strSQL);
					return false;
				}
			} else {
				$this->errc->errors("Tidak bisa menulis data, salah SQLstring -- -- create -- database");
				return false;
			}
		} else {
			$this->errc->errors("SQLstring tidak ada --  -- create -- database");
			return false;
		}	
	}

	
	function delete() {
		if (isset($this->strSQL)) {
			// check apakah SQL string nya bener "delete bla bla" apa engga.
		
			if (substr($this->strSQL,0,6)=="delete") {
				$xhasil = mysql_query($this->strSQL);
				if ($xhasil) {
					return true;
				} else {
					$this->errc->errors("Tidak bisa menghapus data -- query -- delete -- database");
					return false;
				}
			} else {
				$this->errc->errors("Tidak bisa menghapus data, salah SQLstring -- -- delete -- database" );
				return false;
			}
		
		} else {
			$this->errc->errors("SQLstring tidak ada --  -- delete -- database");
			return false;
		}
	
	}
	
	function setFilter($strxFilter) {
		$this->strFilter = $strxFilter;
	}
	
		
	function setTotalRow($strxSQL) {
	
	if ($this->intDebug) echo "<p class=\"error\">$strxSQL</p>";
	
		$xhasil = mysql_query($strxSQL);
		
		if ($intRows = mysql_num_rows($xhasil)) {
		  $this->intTotalRow = $intRows;
		} else {
		  $this->intTotalRow = 0;
		  $this->errc->errors("tidak bisa menghitung jumlah row");
		  
		}
	}
	
	function setLimit($intxAwal=0, $intxLimit) {
		$this->intAwal = $intxAwal;
		$this->intLimit = $intxLimit;
	}
	
	function setGroup($strxGroup) {
		$this->strGroupBy = $strxGroup;
	}
	
	function setDebug($intDebugl) {
		$this->intDebug = $intDebugl;
	}
	function getTotalRow() {
		return $this->intTotalRow;
	}
	
	function getError() {
		if ($this->intDebug) $sql = $this->strSQL;
		return $this->errc->getError() . " *** " . $sql;
	}

	function getstrSQL() {
		return $this->strSQL;
	}
	
	
	function setPage($intxPage) {
	// parameter page harus dikurangi 1
	// alasan : page = 1 -> maka limitnya adalah setLimit(0,3) = setLimit((1 - 1)*3,3)
	//			   page = 2 -> maka limitnya adalah setLimit(3,3) = setLimit((2 - 1)*3,3)
	//				page = 3 -> maka limitnya adalah setLimit(6,3) = setLimit((3 - 1)*3,3)
	
		$this->setLimit($this->intAwal + ($this->intLimit * ($intxPage-1)), $this->intLimit);
	}
}

?>
