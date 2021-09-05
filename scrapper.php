<?php

$pageCrawl = 5;
$crawlUrl = "https://agencyanalytics.com/";



$check_url_crawled = array();
$crawl_data = array();


function checkUrlType($url){
	$check_url = preg_match("@^https?://@", $url);
	return $check_url == 1 ? true : false;
}

function startloadTimer(){
	$start_time = microtime(TRUE);
	return $start_time;
}


function endLoadTimer(){
	$end_time = microtime(TRUE);
	return $end_time;
}


function avgTime($start, $end){
	$time_result = ( $start - $end ) * 1000;
	$time_result = round( $time_result, 5 );
	return $time_result;
}


function scrapped_info($url, $baseUrl) {

	$options = array('http'=>array('ignore_errors' => true, 'method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));

	$href = $styles = $js = $internal=$external=$photo = array(); 

	$context = stream_context_create($options);

	$dom = new DOMDocument();

	$start = microtime(true);
	$fgets = @file_get_contents($url, false, $context);
	@$dom->loadHTML($fgets);
	
	$avg_load_time = microtime(true)-$start;

	$word_count = str_word_count($fgets);

	$title = $dom->getElementsByTagName("title");

	$title = $title->item(0)->nodeValue;

	$links = $dom->getElementsByTagName("a");
	$schemas = $dom->getElementsByTagName("link");
	$scripts = $dom->getElementsByTagName("script");
	$images = $dom->getElementsByTagName("img");

	foreach($images as $image){
		if(checkUrlType($image->getAttribute("href"))){
			$external[] = $image->getAttribute("href");
		}else{
			$internal[] = urlencode($baseUrl.$image->getAttribute("href"));
		}
		$photo[] = urlencode($image->getAttribute("src"));
	}

	foreach($links as $link){
		if(checkUrlType($link->getAttribute("href"))){
			$external[] = $link->getAttribute("href");
		}else{
			$internal[] = urlencode($baseUrl.$link->getAttribute("href"));
		}
		$href[] = urlencode($link->getAttribute("src"));
	}

	foreach($schemas as $schema){
		if(checkUrlType($schema->getAttribute("href"))){
			$external[] = $schema->getAttribute("href");
		}else{
			$internal[] = urlencode($baseUrl.$schema->getAttribute("href"));
		}
		
		$styles[] = $schema->getAttribute("href");
		
	}

	foreach($scripts as $script){
		if(checkUrlType($script->getAttribute("href"))){
			$external[] = $script->getAttribute("href");
		}else{
			$internal[] = urlencode($baseUrl.$script->getAttribute("href"));
		}
		$js[] = $script->getAttribute("src");
		
	}
	
	return [
		'url' => urlencode($url), 
		'title' => $title,
		'internal' => $internal, 
		'external' => $external, 
		'images' => array_unique($photo), 
		'load' => $avg_load_time,
		'word_count' => $word_count,
		'status_code' => $http_response_header
	];

}


function webScrapper($url, $avgUrl) {

	global $check_url_crawled;
	global $crawl_data;

	$options = array('http'=>array('method'=>"GET", 'headers'=>"User-Agent: howBot/0.1\n"));

	$crawl_result = array();

	$count = 0;

	$context = stream_context_create($options);

	$dom = new DOMDocument();

	@$dom->loadHTML(@file_get_contents($url, false, $context));

	$linklist = $dom->getElementsByTagName("a");

	foreach ($linklist as $link) {

		$l =  $link->getAttribute("href");

		$check_url = preg_match("@^https?://@", $l);

		if($check_url == 0){

			if (substr($l, 0, 1) == "/" && substr($l, 0, 2) != "//") {
				$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].$l;
			} else if (substr($l, 0, 2) == "//") {
				$l = parse_url($url)["scheme"].":".$l;
			} else if (substr($l, 0, 2) == "./") {
				$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].dirname(parse_url($url)["path"]).substr($l, 1);
			} else if (substr($l, 0, 1) == "#") {
				$l = parse_url($url)["scheme"]."://".parse_url($url)["host"].parse_url($url)["path"].$l;
			} else if (substr($l, 0, 3) == "../") {
				$l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
			} else if (substr($l, 0, 11) == "javascript:") {
				continue;
			} else if (substr($l, 0, 5) != "https" && substr($l, 0, 4) != "http") {
				$l = parse_url($url)["scheme"]."://".parse_url($url)["host"]."/".$l;
			}
			if($l !== $url && $count <=	 $avgUrl){
				if (!in_array($l, $check_url_crawled)) {
						$check_url_crawled[] = $l;
						$crawl_data[] = $l;
						
						$crawl_result[] = scrapped_info($l, $url);
				}
			}
			
		}
		$count++;
	}

	array_shift($crawl_data);

	foreach ($crawl_data as $site) {
		webScrapper($site, $avgUrl);
	}
	return json_encode($crawl_result);

}




// Begin the crawl_data process by crawl_data the starting link first.
$json = json_decode(webScrapper($crawlUrl, $pageCrawl));
