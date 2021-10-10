<?php

$before = microtime(true);

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

require_once('stemming/wordstemmer.php');

require_once('stemming/search.php');

require_once("summary/SummaryTool.php");

require_once("summary/SentenceTokenizer.php");

$q = $_GET['q'];

$type = $_GET['type'];

$limit = intval($_GET['limit']);

if($_GET['deep_search'] == 1) {

	$deep_search = 1;

}

$page = preg_replace('/[^-0-9]/', '', $_GET['page']);

if($page=="" or $page==" " or $page=="0") {

	$page="1";

}

$q = htmlentities(stripslashes($q), ENT_QUOTES | ENT_IGNORE, "UTF-8");

$q = html_entity_decode($q);

if($q=="") {

}
else if(isset($q)) {

	$q = mysqli($q);
    
	$common = extractCommonWords($q, 100);

	$common = array_keys($common);

	foreach($common as $p) {

		if(!space($p)) {

			$check[] = $p;

			if(strlen($p) > 1 ) {

				$bold[] = $p;

			}

		}

	}


	$expand = array_count_values(str_word_count(strtolower($q), 1));

	$array = web($expand, $common, $q, $page, $limit, $deep_search, $query_row);

	$count = $array['count'];

	$results['stem'] = $array['stem'];

	unset($array['stem']);

	unset($array['count']);

	if($spell = didyoumean($check)) {

		$results['spell'] = $spell;

	}

	foreach ($array as $key => $main) {

        $results['results'][] = array('url' => urlencode($main->url), 'title' => urlencode(make_bold(limiter(urldecode($main->title), 10, array('�')) , $bold)), 'showurl' => urlencode(show_url($main->url)), 'abstract' => urlencode(make_bold(limiter(urldecode($main->abstract), 30, array('�')), $bold)));

    }

	$json = json_encode($results);

	print_r($json);

}

$after = microtime(true);
//echo ($after-$before) . " sec/serialize\n";

?>
