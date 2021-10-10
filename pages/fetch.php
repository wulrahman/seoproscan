<?php

if(intval($count) <= 0) {  

    $array = url_info(strtolower(redirection($domain)), 1, "", "", 1);
    
    if($array['status'] == 200) {

        $array = site_info($array);

        $array = itemscope($array);

        $array['social'] = social_stats($domain);

        if(!empty($array['content'])) {

            $array['main_terms'] = extractCommonWords($array['content'], 20);

            $main_term = array_search(max($array['main_terms']),$array['main_terms']);

            $array['google']['keyword'] = get_keywords($main_term);

        }

        $array['google']['backlink'] = (string)get_backlink($domain);

        $array['alexa'] = (string)alexa_rank($domain);

        $array['site_age'] = (string)$w->age(trim($domain));

        $domain_count = substr_count($domain, '.'); // 2

        $pieces = explode(".", $domain); 

        if($domain_count == 1){ 

            $array['domain_lenght'] = strlen($pieces[0]); 

        }
        else if($domain_count >= 2){ 

            $array['domain_lenght'] = strlen($pieces[1]);

        }

        $array['get_images_count'] = count($array['img']);

        foreach($array['img'] as $key => $image) {

            if(!space($image['alt'])) {

                $array['get_alt_images']++;

            }

        }

        $array['get_alt_miss'] = $array['get_images_count'] - $array['get_alt_images'];

        $array['check_heading'] = 0;

        foreach($array['getheading'] as $heading => $headings){

            if(!empty($headings)){

                $array['check_heading']++;

            }

        }

        $array['get_top_country_alexa'] = get_top_country_alexa($domain);

        $html=file_get_contents("http://www.statshow.com/www/".$domain);

        $dom = new DOMDocument();    
        $dom->loadHTML($html);    

        $finder = new DomXPath($dom); 
        $nodes = $finder->query('//div[@class="show_more_summary"]'); 	

        foreach($nodes as $node){

            $array['summary'][] = $node->nodeValue;

        }

        $array['robot'] = url_info("http://".$domain."/robots.txt");

        $array['getheading']= get_headings_tag($array['response']);

        $array = domain_rank($array);

        $time = $array['curl_info']['total_time'];	

        $curl_info = $array['curl_info'];

        $encoding = $array['main_header']['Content-Encoding'];
        
        $array['gzip']['page_size'] = mb_strlen($array['response'], '8bit');

        $array['gzip']['text_size'] = mb_strlen($array['content'], '8bit');

        $actual_lenght = strlen($array['response']);

        $strip_lenght = strlen($array['content']);

        $array['gzip']['getRatio'] = round((($strip_lenght/$actual_lenght)*100), 2);	

        if($encoding == "gzip") {

            $after_gzip = strlen(gzcompress($array['response']));

            $array['gzip']['convert_before'] = bytesToSize($actual_lenght);

            $array['gzip']['convert_after'] = bytesToSize($after_gzip);

            $array['gzip']['percentage'] = round((($actual_lenght - $after_gzip) / ($actual_lenght)) * 100, 2);

            $array['gzip_enable'] = "true";

        }

        $array['url_WWW'] = 'www.'.$domain;

        $array['parsedRedirect'] = getDomainName($array['url']);

        if($array['parsedRedirect'] == $domain || $array['parsedRedirect'] ==  $array['url_WWW']){

            $array['rate']=$array['rate']+1;


        }

        $who = trim($domain);

        $array['whois'] = (string) LookupDomain($who);

        $new_array = $array;

        $meta = $array['content'];

        $density =  mysqli(implode(',', array_keys($array['main_terms'])));

        $title = $array['title'];

        $description = $array['description'];

        $new_url = $array['url'];

        $response = mysqli($array['response']);

        //$full_text = mysqli($array['full_text']);

        unset($new_array['response']);

        mysqli_query($setting['Lid'], "INSERT INTO `meta` (`word`, `array`, `domain`, `density`, `title`, `description`, `keywords`, `new_url`) VALUE ('".$meta."', '".mysqli(serialize($new_array))."', '".$domain."', '".$density."', '".$title."', '".$description."', '".$keywords."', '".$new_url."')");
        
        $metaid->id = mysqli_insert_id($setting['Lid']);

        mysqli_query($setting['Lid'], "INSERT INTO `html` (`meta`, `html`) VALUE ('".$metaid->id."', '".$response."')");


         
    }
    
    mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `domain`  FROM `domain` WHERE `domain` = '".$domain."' LIMIT 0, 1");

    $domain_count = array_pop(mysqli_fetch_row(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));
    
    if($domain_count > 0) {
        
        mysqli_query($setting['Lid'], 'UPDATE `domain` SET `timestamp`=now(), `status`="'.$array['status'].'" WHERE `domain`="'.$domain.'"');
        
    }
    else {
    
        mysqli_query($setting['Lid'], 'INSERT INTO `domain` (`domain`, `status`) VALUE ("'.$domain.'", "'.$array['status'].'")');
        
    }
    
    header("Refresh:0");

}

?>