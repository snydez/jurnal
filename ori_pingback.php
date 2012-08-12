<?php header("Content-Type: application/xml");
include_once("functions.php");
include_once(CLASSFOLDER ."/database.class.php");
require_once('xmlrpc.inc');
require_once('xmlrpcs.inc');

function pbprocess($m) {
        global $xmlrpcerruser;

        $x1 = $m->getParam(0);
        $x2 = $m->getParam(1);
		$dbcheck1 = new Database($intDebug);
		$dbcheck2 = new Database($intDebug);
		$dbins1 = new Database($intDebug);
		
        $source = $x1->scalarval(); # their article
        $dest = $x2->scalarval(); # your article
		
		// cek source
		if ($source=="") {
		   return new xmlrpcresp(0, 16, "Source uri does not exist");
		}
		// // //
		
		// cek target
		if ($dest=="") {
			return new xmlrpcresp(0, 32, "Target uri does not exist");
		}
		// // // 
		
		$regx = "http://jurnal.snydez.com/id/";
		
		// kalo formatnya nya bukan */ID/ gak boleh terima pingback
		if (!ereg($regx, strtolower($dest), $ketemu)) {
			return new xmlrpcresp(0, 33, "Target uri cannot be used as target");
		}		
		// // //
		
		$regx = "@http://jurnal.snydez.com/id/([^/]+)/?(.*)?/?@si";
		// baca ID nya, terus cek ke database, ada engga, kalo gak ada ga boleh terima pingback		
		if (preg_match($regx, $dest, $ID) ) {
			$strSQL = "select * from tblJurnal";
			$dbcheck1->setstrSQL($strSQL);
			$dbcheck1->setFilter("IDJurnal=" . $ID[1] );
			
		}
		
		
		
		if (!$hasil = $dbcheck1->retrieve()) {
				
			return new xmlrpcresp(0, 50, "Error with query : checking valid ID");
		}
		
		$rowcount= $dbcheck1->getTotalRow();
		
		if ($rowcount == 0) {
			return new xmlrpcresp(0, 32, "Target uri does not exist" );
		}
		// // //
		
		// cek duplicate URL source
		unset($rowcount);
		$strSQL = "select * from tblKoment";
		$dbcheck2->setstrSQL($strSQL);
		$dbcheck2->setFilter("URIKomentator='" . $source . "' and IDJurnal=" . $ID[1]);
		
		
		if (!$hasil = $dbcheck2->retrieve()) {
				return new xmlrpcresp(0, 50, "Error with query : checking duplicate ping");
		}
		$rowcount= $dbcheck2->getTotalRow();
		
		if ($rowcount > 0) {
			return new xmlrpcresp(0, 48, "duplicate ping not allowed");
		}
		// // //
		
		// checking postingannya si source ada link ke dest ga?
		$parts = parse_url($source);
		
		if (!isset($parts['scheme'])) {
            		return new xmlrpcresp(0, 50, "Error no source uri scheme"  );
            	}
        	if ($parts['scheme'] != 'http') {
			return new xmlrpcresp(0, 50, "Error source uri is not http"  );
            	}
        	if (!isset($parts['host'])) {
            		return new xmlrpcresp(0, 50, "Error source uri no host"  );
            	}
        
		$host = $parts['host'];
        	$port = 80;
        
			if (isset($parts['port'])) $port = $parts['port'];
        	$path = "/";
             if (isset($parts['path'])) $path = $parts['path'];
             if (isset($parts['query'])) $path .="?".$parts['query'];
             if (isset($parts['fragment'])) $path .="#".$parts['fragment'];

        
		$fp = fsockopen($host, $port);
             fwrite($fp, "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n");
        $sourcepost = "";
             while (is_resource($fp) && $fp && (!feof($fp))) {
                     $sourcepost .= fread($fp, 1024);
                     }
        fclose($fp);
        
		$regx = "@http://jurnal.snydez.com/id/([^/]+)/?(.*)?/?@si";
		preg_match($regx, $dest, $result);
		$tobecheck = "@http://jurnal.snydez.com/id/" . $result[1] . "@si";
		
		if (!preg_match($tobecheck, $sourcepost, $resultcheck )) {
			return new xmlrpcresp(0, 17, "Source uri doesn't have link to target uri");
		}
		//
		

		$regx = "@<title>(.*?)</title>@si";
		preg_match($regx, $sourcepost, $title);




		// gak tau mana yang worked
		$regstr1 = '%(?<=<p>|<br\/>)(.*?)<a[^<]*href=\"http:\/\/jurnal.snydez.com\/id\/' .$ID[1] .'/?[^\"]*\"[^>]*>(.*?)</a>(.*?)(<br\/>|<\/p>)%i';
	
		$regstr2 = '%(.*?)<a[^<]*href=\"http:\/\/jurnal.snydez.com\/id\/' . $ID[1] . '/?[^\"]*\"[^>]*>(.*?)</a>(.*?)%';
		

		if (preg_match($regstr1, $sourcepost, $thefound)==0) 
			preg_match($regstr2, $sourcepost, $thefound);
		
		//$sourcepost = stripslashes($sourcepost);
	
// $sourcepost = htmlentities($sourcepost , ENT_QUOTES);
		$sourcepost = $thefound[1] . "<u>" . $thefound[2] . "</u>" . $thefound[3];
		$sourcepost = strip_tags($sourcepost, "<u>");	
	$sourcepost = htmlentities($sourcepost, ENT_QUOTES );	

		// setelah terfilter oleh macem macem error, harusnya ini tidak error terus simpen ke database
		
		
		$strSQL = "insert into tblKoment (strKomentator, dtmTanggal, strKoment, emailKomentator, IDJurnal, intType, URIKomentator)";
		$strSQL .= " values ('" . $title[1] . "',now(),'" . $sourcepost . "','noemail@forpi.ng'," . $ID[1] . ",2,'" . $source . "')";
		/* intType = 2 = Receive pingback   */
		$dbins1->setstrSQL($strSQL);
		
		unset($hasil);
		if (!$hasil = $dbins1->create()) {
			 return new xmlrpcresp(0, 50, "Error with query inserting pingback " );
		}
		
			return new xmlrpcresp(new xmlrpcval("Pingback registered. enjoyed my post.", "string"));
        }
		
$a = array( "pingback.ping" => array( "function" => "pbprocess" ));

$s = new xmlrpc_server($a, true);
$s->setdebug($intDebug);
$s->service();
?>
