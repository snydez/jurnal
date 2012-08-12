<?php

class Secret {
	var $strToEncode;

					   

	function toencode($strxToEncode="") {
		
		if ($strxToEncode != "") $this->strToEncode = $strxToEncode;
		$tmpx = "";
		if (isset($this->strToEncode)) {
			for ($i=0;$i<strlen($this->strToEncode);$i++) {
				$tmpx = $tmpx . $this->encode($this->strToEncode[$i]);
			}
			return $tmpx;
		}
	}
	
	function encode($chrxa) {
	
		switch ($chrxa) {
			case "a": $tmp="i"; break;
			case "b": $tmp="c"; break;
			case "c": $tmp="b"; break;
			case "d": $tmp="f"; break;
			case "e": $tmp="o"; break;
			case "f": $tmp="d"; break;
			case "g": $tmp="h"; break;
			case "h": $tmp="g"; break;
			case "i": $tmp="a"; break;
			case "j": $tmp="k"; break;
			case "k": $tmp="j"; break;
			case "l": $tmp="m"; break;
			case "m": $tmp="l"; break;
			case "n": $tmp="p"; break;
			case "o": $tmp="e"; break;
			case "p": $tmp="n"; break;
			case "q": $tmp="r"; break;
			case "r": $tmp="q"; break;
			case "s": $tmp="t"; break;
			case "t": $tmp="s"; break;
			case "u": $tmp="y"; break;
			case "v": $tmp="w"; break;
			case "w": $tmp="v"; break;
			case "x": $tmp="z"; break;
			case "y": $tmp="u"; break;
			case "z": $tmp="x"; break;
			
			case "A": $tmp="I"; break;
			case "B": $tmp="C"; break;
			case "C": $tmp="B"; break;
			case "D": $tmp="F"; break;
			case "E": $tmp="O"; break;
			case "F": $tmp="D"; break;
			case "G": $tmp="H"; break;
			case "H": $tmp="G"; break;
			case "I": $tmp="A"; break;
			case "J": $tmp="K"; break;
			case "K": $tmp="J"; break;
			case "L": $tmp="M"; break;
			case "M": $tmp="L"; break;
			case "N": $tmp="P"; break;
			case "O": $tmp="E"; break;
			case "P": $tmp="N"; break;
			case "Q": $tmp="R"; break;
			case "R": $tmp="Q"; break;
			case "S": $tmp="T"; break;
			case "T": $tmp="S"; break;
			case "U": $tmp="Y"; break;
			case "V": $tmp="W"; break;
			case "W": $tmp="V"; break;
			case "X": $tmp="Z"; break;
			case "Y": $tmp="U"; break;
			case "Z": $tmp="X"; break;

			
			case "1": $tmp="2"; break;
			case "2": $tmp="1"; break;
			case "3": $tmp="4"; break;
			case "4": $tmp="3"; break;
			case "5": $tmp="6"; break;
			case "6": $tmp="5"; break;
			case "7": $tmp="8"; break;
			case "8": $tmp="7"; break;
			case "9": $tmp="0"; break;
			case "0": $tmp="9"; break;

			default :	$tmp=$chrxa;
			
		}
		
	return $tmp;
	
	}
	
	


}
?>