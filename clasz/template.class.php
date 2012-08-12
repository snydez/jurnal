<?php
/**
*   @filename : template.class.php
*   @Author   : Salman AS <salmanas@programmer.net>
*   @Comments : This template parser intended for educational purpose only not for end products. 
*   @ChangeLog: 03/12/2001 - First functional form
*   modified by: sonny dezerial - mail@snydez.com
*/

include_once("errors.class.php");

define("ltag", "{");
define("rtag", "}");

class SimpleTemplate {
    var $Tag = array();
    var $Templates = array();
    var $Contents;
	var $theErrors;
    
	
    function Define($handle, $filename)
    {
		$this->theErrors = new Errorc;
		//empty($handle) ||
		
		if ( !file_exists($filename)) {
			$this->theErrors->errors("Error on Defining $filename as $handle\n");
			return(FALSE);
		}
        $this->Templates[$handle]=$filename;
	    return(TRUE);
    }
    
    function Assign($tag, $value)
    {
    if (empty($tag)) {
	    $this->theErrors->errors("Error on Assigning $value to $tagn");
		return(FALSE);
		}
    
		$this->Tag[$tag]=$value;
		return(TRUE);
    }
    
    function Parse($handle)
    {
		reset($this->Tag);
		reset($this->Templates);
		if (file_exists($this->Templates[$handle])) {
			$template=implode("", file($this->Templates[$handle]));
			while (list($tag, $value)=each($this->Tag)) {
				$tag=ltag.$tag.rtag;
				$template=str_replace($tag, $value, $template);
			}
    	
			return($template);
		} else {
			$this->theErrors->errors($this->Template[$handle] . "error" . $handle );
			return false;
		}
    }
    
	function getError() {
		return $this->theErrors->getError();
	}
	
    } //end class
?>