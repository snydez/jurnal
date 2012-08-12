<?php
header('Content-Type: text/xml');
include_once("param.php");

$query = "select * from tbljurnal ";
$query .= "order by ID desc";

$hasil = mysql_query ($query)
   or die ("aasql error");


$baris = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
$baris .= "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";

$template .= "<url>\n";
$template .= "<loc>";
$template .= "{LINK}";
$template .= "</loc>\n";
$template .= "<lastmod>";
$template .= "{LASTMOD}";
$template .= "</lastmod>\n";
$template .= "<changefreq>weekly</changefreq>\n";
$template .= "<priority>{PRIOR}</priority>\n";
$template .= "</url>\n";

$barisend = "</urlset>\n";

$result = str_replace( "{LINK}", "http://jurnal.snydez.com",$template);
$result = str_replace( "{LASTMOD}", date('Y-m-d'),$result);
$result = str_replace( "{PRIOR}", "0.9",$result);
$arrResult[] = $result;


$result = str_replace( "{LINK}", "http://jurnal.snydez.com/hal/1/ord/ASC", $template);
$result = str_replace( "{LASTMOD}", date('Y-m-d'),$result);
$result = str_replace( "{PRIOR}", "0.2",$result);
$arrResult[] = $result;


$result = str_replace( "{LINK}", "http://jurnal.snydez.com/hal/2",$template);
$result = str_replace( "{LASTMOD}", date('Y-m-d'),$result);
$result = str_replace( "{PRIOR}", "0.3",$result);
$arrResult[] = $result;


while ($row = mysql_fetch_array($hasil)) {
	$result = "";
	
	if ($row['strJudul']) {
			$judulpost = $row['strJudul'];
	  } else {
			$judulpost = substr($row['strJudul'],0,20);
	}
  
   if (!$forhyperlink = urlencode(strip_tags($row['strJudulDeskripsi'])))  {
	$forhyperlink = urlencode(strip_tags($judulpost));
   }
	
	$result = str_replace("{LINK}", "http://jurnal.snydez.com/id/" . $row["ID"] . "/". $forhyperlink ,$template);
	$result = str_replace("{LASTMOD}", date('Y-m-d',strtotime($row["dtmTgl"])),$result);
	$result = str_replace( "{PRIOR}", "0.6",$result);

	$arrResult[] = $result;

}


$query = "Select Link_ID, max(dtmTgl) as maxdate from tblkoment group by Link_ID";
$hasilkom = mysql_query ($query)
   or die ("aasql error");

while ($rowkom = mysql_fetch_array($hasilkom)) {
	$result = "";
	$result = str_replace("{LINK}", "http://jurnal.snydez.com/koment/" . $rowkom["Link_ID"] ,$template);
	$result = str_replace("{LASTMOD}", date('Y-m-d',strtotime($rowkom["maxdate"])),$result);
	$result = str_replace( "{PRIOR}", "0.5",$result);

	$arrResult[] = $result;
}




$query = "select c.category, max(j.dtmTgl) as tanggal ";
$query .= " from tblcategory c inner join tbljurnal j on c.Link_ID = j.ID " ;
$query .= " where category <> '' group by c.category ";

$hasilcat = mysql_query ($query)
   or die ("category error");

while ($rowcat = mysql_fetch_array($hasilcat)) {
	$result = "";
	$result = str_replace("{LINK}", "http://jurnal.snydez.com/cat/" .  urlencode($rowcat["category"]),$template);
	$result = str_replace("{LASTMOD}", date('Y-m-d',  strtotime($rowcat["tanggal"])) ,$result);
      $result = str_replace( "{PRIOR}", "0.4",$result);

	$arrResult[] = $result;

}


echo $baris;
foreach ($arrResult as $thebaris) {
	echo $thebaris;
}
echo $barisend;


?>