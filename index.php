<?php

ob_start();

session_start();

ini_set('max_execution_time', 0); 

ini_set('memory_limit', '-1');	

require_once('setting.php');

require_once('common.php');

require_once('function.php');

require_once('include/Html2Text.php');

require_once("include/portable-utf8.php");

require_once('include/simple_html_dom.php');

require_once('include/whois.php');

$url = $_GET['domain'];

$_SESSION['url'] = $url; 

if(space($url)) {
    
   $url = $_SESSION['url']; 
    
}

$url = trim($url);

if(!empty($url) && strpos($url,"mailto") != 'FALSE' && strpos($url,"data") != 'FALSE' && strpos($url,"http://") != 'FALSE' && strpos($url,"https://") != 'FALSE') {

    if(substr($url, 0, 1) == ':') {

        $url = substr($url, 1);

    }

    if(substr($url, 0, 2) == '//') {

        $url = substr($url, 2);

    }

    $url = "http://".$url;

}


$url = mysqli(fix_url($url, $parse));

$array['rate'] = 0;

$array['pass'] = 0;

$array['fail'] = 0;

$parse = parse_url($url);

$parse = main_domain($parse['scheme']."://".$parse['host']);

$domain = mysqli($parse['main'].$parse['extension']);

$dn = ucwords($domain);

$action = $_GET['action'];

if(!space($domain)) {
    
    $domain_time = 'interval 1 day';

    $status = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `domain`, `id`, `status` FROM `domain` WHERE `timestamp` >= now() - ".$domain_time." AND `domain` = '".$domain."' LIMIT 0, 1");
    
    $status_row = mysqli_fetch_object($status);

    $count = array_pop(mysqli_fetch_row(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));
    
    if($count > 0) {
        
        if($status_row->status == '200') {
        
            if($action == "rawdata") {

                require_once('pages/rawdata.php');

            }
            else {

                require_once('pages/result.php');

            }
            
        }
        else {

            require_once('pages/main.php');

        }
    
    }
    else {
        
        require_once('pages/fetch.php');
        
    }
        

}
else {
    
    require_once('pages/main.php');
    
}

//unset($array['response']);

//print("<pre>".print_r($array,true)."</pre>");

?>