<?php
class Errorc {
	var $strErrors;
	

	function __construct(){
	}

	function errors($xstrErrMsg) {
		$this->strErrors[]=$xstrErrMsg;
    }
	
	function getError() {
		if (is_array($this->strErrors)) {
			foreach ($this->strErrors as $xstrError) {
				$xtmpErr .= $xstrError . "<br/>\n";
			}
		}
	
		if ($xtmpErr!="") {
			$xtmpErr = "<div class=\"error\">" . $xtmpErr . "</div>";
		}
		
		return $xtmpErr;
    }

}

?>
