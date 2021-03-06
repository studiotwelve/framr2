<?php date_default_timezone_set("America/New_York");
$start = microtime(true);
  
global $vars;
$vars = array();

$vars["site"] = array(
  "domain" => "foreverincharcoal.com",
  "name" => "Forever In Charcoal",
  "slogan" => "Fine Art Charcoal Portraits by Adrienne Mollette.",
  
  "contact" => array(
    "phone" => "828 564 6536",
    "email" => "foreverincharcoal@gmail.com",
  ),
  
  "location" => array(
    "address" => array(
      "street" => array("3211 Dellwood Road", ""),
      "city" => "Waynesville",
      "state" => "North Carolina",
      "zip" => "28786",
    ),
  ),
);

$vars["site"]["head_title"] = $vars["site"]["name"]." | ".$vars["site"]["slogan"];

function date_now($format = null){
	$now = array(
		"Y" 				=> date("Y"),
		"m" 				=> date("m"),
		"W" 				=> date("W"),
		"d" 				=> date("d"),
		"t" 				=> date("h:i:s a"),
		"T" 				=> date("H:i:s"),
		"U" 				=> date("U"),
		"short" 		=> date("d/m/Y h:i:s a"),
		"long" 		  => date("l, F jS, Y h:i:s a T"),
		"path"      => date("Y/m/d"),
	);
	
	if(!$format){
  	return $now;
	} else {
  	return array($format => $now[$format]);
	}
}

define("ROOT", dirname(__FILE__));
set_include_path(get_include_path().PATH_SEPARATOR.ROOT);

define("URL", ((!empty($_SERVER['HTTPS']) && strcasecmp($_SERVER['HTTPS'], 'on') === 0 || !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0) ? 'https://' : 'http://').
    (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'].'@' : '').
    (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'].
    ($https && $_SERVER['SERVER_PORT'] === 443 ||
    $_SERVER['SERVER_PORT'] === 80 ? '' : ':'.$_SERVER['SERVER_PORT']))).
    substr($_SERVER['SCRIPT_NAME'],0, strrpos($_SERVER['SCRIPT_NAME'], '/')));
    
define("DOMAIN", isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME']);

function request_path(){
	static $path;
	
	if(isset($path)){
		return $path;	
	}
	
	if(isset($_GET["q"])&&is_string($_GET["q"])){
		$path = $_GET["q"];
		
	}elseif(isset($_SERVER["REQUEST_URI"])){
		$request_path = strtok($_SERVER["REQUEST_URI"], "?");
		
		$base_path_len = strlen(rtrim(dirname($_SERVER["SCRIPT_NAME"]), '\/'));
		
		$path = substr(urldecode($request_path), $base_path_len + 1);
		
		if($path == basename($_SERVER["PHP_SELF"])){
			$path = '';
		}
	}else{
		$path = '';
	}
	
	$path = trim($path, '/');
	
	return $path;
}

function q(){
	$_GET['q'] = request_path();
}

q();

function is_front(){
  $q = false;
  
  if(empty($_GET["q"])){
    $q = true;
  }
  
  return $q;
}

include("inc/buffer.inc");

global $path;
$path = $_GET["q"];
  
if(empty($_GET["q"])){
  $path = "default";
}

function preprocess_page(){
  extract($GLOBALS["vars"]["site"], EXTR_PREFIX_ALL, "site_");
  
  $path = $GLOBALS["path"];
  $page_title = str_replace("|", "-", $GLOBALS["vars"]["site"]["head_title"]);
  $head_title = $GLOBALS["vars"]["site"]["head_title"];
  $content = parse("static/default.php");
  
  switch($path){
    case "payment/cancelled":
      $page_title = "Payment Cancelled";
      $head_title = "$page_title: $head_title";
      $content = "<h1>$page_title</h1><div class=\"alert alert-error\"><p>Oops! Your payment was cancelled, please try again...</p></div> $content";
      break;
    case "payment/received":
      $page_title = "Payment Received";
      $head_title = "$page_title: $head_title";
      $content = "<h1>$page_title</h1><div class=\"alert alert-success\"><p>Your payment is being processed, Thank you...</p></div> $content";
      break;
    case "payment":
      $page_title = "Make a Payment";
      $head_title = "$page_title: $head_title";
      $content = "<h1>$page_title</h1><div class=\"alert alert-info\"><p>To make a payment please click <em>\"Buy Now\"</em> below, Thank you...</p></div> $content";
      break;
  }
  
  $GLOBALS["page_title"] = $page_title;
  $GLOBALS["head_title"] = $head_title;
  $GLOBALS["content"] = $content;
    
  $GLOBALS["execution_time"] = number_format(microtime(true)-$GLOBALS["start"],2);
  
  return parse("tpl.php");
}

print preprocess_page();

//include("tpl.php");

print "<!-- Load time: ".number_format(microtime(true)-$start,2)." -->";