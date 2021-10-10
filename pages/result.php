<?php

if(intval($count) > 0) {  
    
    $query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `domain`, `id`, `array`  FROM `meta` WHERE `domain` = '".$domain."' LIMIT 0, 1");

    $count = array_pop(mysqli_fetch_row(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));
    
    $row = mysqli_fetch_object($query);

    $array = unserialize($row->array);
    
    $compare = mysqli_query($setting['Lid'], 'SELECT SQL_CALC_FOUND_ROWS * FROM `meta` WHERE `domain`="'.$row->domain.'" AND `id` !="'.$row->id.'" ORDER BY `meta`.`id`  DESC');

    $compare_count = array_pop(mysqli_fetch_row(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));
    
    if($compare_count > 0) {
        
        $row_compare = mysqli_fetch_object($compare);

        $compare = unserialize($row_compare->array);
        
        $speed_percentage = round((($array['curl_info']['total_time'] - $compare['curl_info']['total_time']) / $array['curl_info']['total_time'])*100);

        $size_percentage = round((($array['curl_info']['size_download'] - $compare['curl_info']['size_download']) / $array['curl_info']['size_download'])*100);

        
    }

    require_once("include/header.php");

    require_once("include/main_header.php");

    ?>

    <div class="result_main" id="otherbody">
        
        <div>

            <?php

            include('include/ads.php');

            ?>

        </div>
        
        <div class="main_icon">
            
            <?php
            
            $main_color = "#eeeeee";
            
            if(!space($array['theme-color'])) {
            
                $main_color = $array['theme-color'];
            
            }
            
            if(!space($array['og:image'])) { 
                
                $main_parse = parse_url($array["url"]);
	
                $array['og:image'] = fix_url($array['og:image'], $main_parse) ?>
            
                <div class="mobile_display pure-u-1-5">
                    
                    <div class="test_bentch" style="background-image: url(<?=$array['og:image']?>);"></div>
            
                </div><div class="mobile_display pure-u-4-5"><?php
                    
            }
            else { 
                
                echo '<div>';
                 
            }
            
            ?>

                <div class="main_search" style="background-color:<?=$main_color?>;">

                    <form action="<?=$setting['site_url']?>" method="GET">

                        <div class="input-group">

                            <input type="text" name="domain" value="<?=stripslashes($domain)?>" placeholder="Scan a website now. Example : Cragglist.com"><input type="submit" name="submit" value="Fetch" />

                        </div>

                    </form>	

                </div>
            
            </div>
            
        </div>
        
        <div class="main_matrics">

            <div class="mobile_display pure-u-1-3">

                <div class="metric">
                    
                    <?php
                
                    $site_name = $dn;

                    if(!space($array['og:site_name'])) {
                        
                        $site_name = $array['og:site_name'];
                        
                    }
    
                    ?>

                    <span class="number"><?=$site_name?></span>

                    <span class="title">Site Name</span>

                </div>

            </div><div class="mobile_display pure-u-1-3">

                <div class="metric">

                    <span class="number"><?=$array['site_age']?></span>

                    <span class="title">Domain Age</span>

                </div>

            </div><div class="mobile_display pure-u-1-3">

                <div class="metric">

                    <span class="number"><?=$array['alexa']?></span>

                    <span class="title">Domain Alexa</span>

                </div>

            </div>

        </div>

        <div class="main_header_info">

            <div class="main_top_info">

                <div class="mobile_display pure-u-3-5">

                    <div class="profile-main">

                        <img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" data-url="<?="http://".$domain?>" alt="Google">

                    </div>

                </div><div class="mobile_display pure-u-2-5">

                    <div class="weekly-summary text-right">

                        <span class="number"><?=$array['google']['backlink']?></span>

                        <span class="percentage"><i class="fa fa-caret-up text-success"></i> 12%</span>

                        <span class="info-label">Total Google Backlinks</span>

                    </div>

                    <div class="weekly-summary text-right">

                        <span class="number"><?=round($array['curl_info']['total_time'], 2)?> Sec</span> 
                        
                        <?php
        
                        if(isset($speed_percentage) > 0) { ?>

                            <span class="percentage"><?php

                              if ($speed_percentage < 0) {

                                echo '<i class="fa fa-caret-down text-danger"></i>';

                              }
                              else {

                                echo '<i class="fa fa-caret-up text-success"></i>';

                              }

                              ?> <?=$speed_percentage?>%</span>
                        
                            <?php
                              
                        }
                        
                        ?>
                        
                        <span class="info-label">Website Speed</span>

                    </div>

                    <div class="weekly-summary text-right">

                        <span class="number"><?=($array['curl_info']['size_download']/1000)?>KB</span> 
                        
                        <?php
        
                        if(isset($size_percentage) > 0) { ?>

                            <span class="percentage"><?php

                              if ($size_percentage < 0) {

                                echo '<i class="fa fa-caret-down text-danger"></i>';

                              }
                              else {

                                echo '<i class="fa fa-caret-up text-success"></i>';

                              }

                              ?> <?=$size_percentage?>%</span>

                            <?php
                              
                        }
                        
                        ?>
                        
                        <span class="info-label">WebPage Size</span>

                    </div>

                </div>

            </div>
            
        </div>

        <div class="web_info_panel mobile_display pure-u-3-5">

            <div class="main_panel">

                <h3>Website Traffic Estimator</h3>

                <div class="main_info">
                    
                    <?php
        
                    $value_paragraph = 3;
                    
                    $text = str_replace("\r\n","\n", implode(" ", $array['summary']));

                    $paragraphs = preg_split("/(?<=[.?!])\s+(?=[a-z])/i",$text);
    
                    $count_paragraphs = round((count($paragraphs)/$value_paragraph), 0, PHP_ROUND_HALF_UP);
    
                    for ($i = 0; $i <= $count_paragraphs ;$i++) {
                        
                        $new_paragraphs[($value_paragraph*$i)] = "<p>".$paragraphs[((1  + $value_paragraph)*$i)].' '.$paragraphs[(1+ ((1  + $value_paragraph)*$i))].' '.$paragraphs[($value_paragraph*($i + 1))]."</p>";

                    }

                    $text = implode("", $new_paragraphs);
    
                    ?>

                    <div><?=$text?></div>
                    
                </div>

            </div>
                
        </div><div class="mobile_display pure-u-2-5">

            <div class="mobile_display web_traffic_panel">
        
                <h3>Traffic Location</h3>

                <div>

                    <ul class="population_header">

                        <li class="pure-u-1-2">
                            
                            <span class="title">Popular Countries</span>
                        
                        </li><li class="pure-u-1-2">
                        
                            <span class="title">Percent of Visitors</span>
                        
                        </li>

                    </ul>

                    <ul class="population_data">

                        <?php

                        if($array['get_top_country_alexa'] !='null') {

                            $i = 0; 

                            foreach($array['get_top_country_alexa'] as $link => $stat) { ?>

                                <li>
                                    
                                    <div class="pure-u-1-2">

                                        <span class="data"><?=ucwords($stat["country"])?></span>

                                    </div><div class="pure-u-1-2">

                                        <span class="data"><?=ucwords($stat["percent"])?></span>
                                    
                                    </div>
                                    
                                </li>
                        
                            <?php

                            }

                        } 
                        else {

                            echo '<li>
                            <div>No data</div>
                            </li>';

                        }	

                        ?>

                    </ul>

                </div>

            </div>
            
        </div>

        <div class="site_score_seoproscan">
        
            <div class="pure-u-1-3">

                <div class="metric">

                    <span class="number"><?=$array['rate']+35?></span>

                    <span class="title">Total Score</span>

                </div>

            </div><div class="pure-u-1-3">

                <div class="metric">

                    <span class="number"><?=$array['pass']?></span>

                    <span class="title">Passed</span>

                </div>

            </div><div class="pure-u-1-3">

                <div class="metric">

                    <span class="number"><?=$array['fail']?></span>

                    <span class="title">Failed</span>

                </div>

            </div>
            
        </div>

        <div class="mobile_display pure-u-1-2">

            <div class="mobile_display main_header_panel">

                <h3>Website Heading Tags</h3>
                
                <div class="main_panel">

                    <div class="text">

                        <ul class="header_tags">

                            <?php

                            foreach($array['getheading'] as $heading => $headings) {

                                echo '<li class="pure-u-1-6"><div class="tags">'.strtoupper($heading).'</div></li>';

                            }

                            ?>

                        </ul>
                        
                        <ul class="header_counts">
                            
                            <?php

                            foreach($array['getheading'] as $headings) {

                                echo '<li class="pure-u-1-6"><div class="count">'.count($headings).'</div></li>';

                            }

                            ?>
                            
                        </ul>

                        <ul class="header_headers">

                            <?php

                            $i=0;

                            foreach($array['getheading'] as $heading => $headings){

                                if(!empty($headings)){

                                    foreach($headings as $h){

                                        echo '<li><span>'.strtoupper($heading) .'</span> '.$h.'</li>';

                                    }
                                    
                                } 

                            }

                            ?>
                            
                        </ul>

                    </div>

                </div>

            </div>

        </div><div class="mobile_display pure-u-1-2">

            <div class="mobile_display main_seo_panel">
                
                <h3>OnPage SEO</h3>

                <div class="main_seo">

                    <ul>

                        <li>

                            <p>
                                <span class="title">Title Tag</span>

                                <span class="short-description">

                                    <?php 

                                    if(!empty($array['title'])){

                                        echo stripslashes($array['title'])." | ( ".strlen($array['title'])." Characters )";

                                    }
                                    else {

                                        echo "No title tag used.";

                                    }

                                    ?>

                                </span>

                            </p>

                        </li>

                        <li>

                            <p>

                                <span class="title">Description Tag</span>

                                <span class="short-description">

                                    <?php

                                    if(!empty($array['description'])) {

                                        echo stripslashes($array['description'])." | ( ".strlen($array['description'])." Characters )";

                                    }
                                    else {

                                        echo "No Description tag used.";

                                    }				

                                    ?>

                                </span>

                            </p>

                        </li>

                        <li>
                            
                            <p>

                                <span class="title">Google SERP Snippet</span>

                                <span class="short-description">

                                    <div class="google_title"><?=stripslashes($array['title'])?></div>

                                    <div class="google_url"><?="http://".$domain?></div>

                                    <div class="google_desc"><?=stripslashes($array['description'])?></div>	

                                </span>

                                <span class="date">Just Updated</span>

                            </p>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

        <div class="mobile_display pure-u-1-2">

            <div class="mobile_display main_html_panel">

                <h3>Html Tag Details</h3>

                <div>

                    <ul>

                        <li>

                            <p>Image ALT Attribute <span class="label-percent">23%</span></p>

                            We found <strong><?=$array['get_images_count']?></strong> images on this web page

                            <?php

                            if($array['get_alt_miss'] == 0){	

                                echo '<p> <img src="files/img/true.png" alt="ALT attributes"> Good, most or all of your images have alt attributes.</p>';

                            }
                            else{

                                echo '<p> <img src="files/img/false.png" alt="ALT attributes"> Bad, <strong>'.$array['get_alt_miss'].'</strong> missing alt attributes.</p>';

                            }


                            ?>

                        </li>

                        <li>
                            <p>Text to HTML Ratio <span class="label-percent">80%</span></p>

                            <?=$array['gzip']['getRatio']?>% <small>(Text size <?=bytesToSize($array['gzip']['text_size'])?> and  Code size <?=bytesToSize($array['gzip']['page_size'])?>)</small>

                        </li>

                        <li>

                            <p>Website Compression Rate <span class="label-percent">100%</span></p>

                            <?php

                            if($array['gzip_enable'] == true){		

                                echo '<p> <img src="files/img/true.png" alt="Gzip enable"> <strong>'. $array['gzip']['convert_before'].'</strong> to <strong>'.$array['gzip']['convert_after'].'</strong> (<strong>'.$array['gzip']['percentage'].' % size savings</strong>)</p>';

                            }
                            else{

                                echo '<p> <img src="files/img/false.png" alt="Gzip enable"> <strong>Website is not Compressed.</strong></p>';		

                            }

                            ?>	

                        </li>

                        <li>

                            <p>Website URL Canonicalization <span class="label-percent">45%</span></p>

                            <?php

                            $isUrlCanonicalization = 'No, your site\'s url '.$array['url_WWW'].' and '.$domain.' don\'t resolve to the same URL.';

                            if($array['get_redirect']==null) {

                                $isUrlCanonicalization = 'Yes, your site\'s url <a href="http://'.$array['url_WWW'].'" target="_blank" rel="nofollow">'.$array['url_WWW'].'</a> and <a href="http://'.$domain.'" target="_blank" rel="nofollow">'.$domain.'</a> resolve to the same URL.';

                            }
                            else {

                                if($array['parsedRedirect'] == $domain || $array['parsedRedirect'] ==  $array['url_WWW']) {

                                    $isUrlCanonicalization = 'Yes, your site\'s url <a href="http://'.$array['url_WWW'].'" target="_blank" rel="nofollow">'.$array['url_WWW'].'</a> and <a href="http://'.$domain.'" target="_blank" rel="nofollow">'.$domain.'</a> resolve to the same URL.';

                                }

                            }

                            ?>

                            <div>

                                <?=$isUrlCanonicalization?>

                            </div>

                        </li>

                        <li>

                            <p>Robots.txt Existence <span class="label-percent">10%</span></p>

                            <?php

                            if($array['robot']['status'] == 200) {

                                echo "<img src='files/img/true.png' alt='Robots.txt found'><a href='http://".$domain."/robots.txt' >Robots.txt</a>.";

                            }
                            else{

                                echo "<img src='files/img/false.png' alt='No Robots.txt found'>.";

                            }

                            ?>

                        </li>

                    </ul>

                </div>

            </div>

        </div><div class="mobile_display pure-u-1-2">

            <div class="mobile_display social_panel">
                
                <h3>Social Media Data</h3>

                <ul>
                    
                    <li class="pure-u-1-2">
                        
                        <div class="main_social">

                            <span class="title">Facebook</span>

                            <span class="data"><?=$array['social']['facebook']['share']?></span>
                            
                        </div>

                    </li><li class="pure-u-1-2">
                    
                        <div class="main_social">
                         
                            <span class="title">Buffer</span>

                            <span class="data"><?=$array['social']['buffer']?></span>
                            
                        </div>

                    </li><li class="pure-u-1-2">
                    
                        <div class="main_social">

                            <span class="title">Pinterest</span>

                            <span class="data"><?=$array['social']['pinterest']?></span>
                            
                        </div>

                    </li><li class="pure-u-1-2">
                    
                        <div class="main_social">

                            <span class="title">LinkedIn</span>

                            <span class="data"><?=$array['social']['linkedin']?></span>
                            
                        </div>

                    </li><li class="pure-u-1-2">
                    
                        <div class="main_social">

                            <span class="title">Stumbleupon</span>

                            <span class="data"><?=$array['social']['stumbleupon']?></span>
                            
                        </div>
                        
                    </li><li class="pure-u-1-2">
                    
                        <div class="main_social">

                            <span class="title">Google +</span>

                            <span class="data"><?=$array['social']['google']?></span>		
                            
                        </div>
                        
                    </li>

                </ul>

            </div>

        </div>
        
        <div class="mobile_display pure-u-1-2">

            <div class="mobile_display url_main_panel">

                <h3>Internal Links</h3>

                <div>
                    
                    <ul>
                        
                        <?php

                        foreach ($array['a'] as $link) {

                            if (strpos($link['href'], $domain) !== false) {

                                echo "<li>".$link['href']."</li>";

                            }

                        }

                        ?>	
                        
                    
                    </ul>

                </div>
                
                <h3>External Links</h3>

                <div>
                    
                    <ul>
                        
                        <?php

                        foreach ($array['a'] as $link) {

                            if (strpos($link['href'], $domain) == false) {

                                echo "<li>".$link['href']."</li>";

                            }

                        }

                        ?>	
                        
                    
                    </ul>

                </div>

            </div>

        </div><div class="mobile_display pure-u-1-2">

            <div class="mobile_display keyword_main_panel">

                <h3>Keyword density</h3>
                
                <div>
                    
                    <ul class="keyword_main">

                        <?php

                        if(!empty($array['content'])) {

                            foreach($array['main_terms'] as $term => $count) {

                                echo "<li><b>$term</b> ( $count )</li>";	

                            }


                        }
                        else {

                            echo "Website can't be loaded.";

                        }	

                        ?>
                        
                    </ul>

                </div>
                
                <h3>Keyword Suggestion</h3>

                <div>
                    
                    <ul class="keyword_suggestion">

                        <?php

                        foreach($array['google']['keyword'] as $key => $keyword)  {

                            echo "<li>".$keyword."</li>";	


                        }

                        ?>
                        
                    </ul>

                </div>
                
            </div>

        </div>

        <div>

            <div class="whois_main_panel">

                <div><pre><?=$array['whois']?></pre></div>

            </div>

        </div>

    </div>

    <?php

    require_once('include/footer.php');

    ?>

    </div>

    </body>

    </html>

<?php
    
}

?>
