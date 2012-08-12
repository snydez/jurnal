<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once("functions.php");


include_once( CLASSFOLDER . "/sitemap.class.php");

$intDebug = 1;

$x = new Sitemap($intDebug);
echo $x->generateSitemaps();
echo $x->getError();


?>
