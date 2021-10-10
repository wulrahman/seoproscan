<?php

require_once("include/header.php");

require_once("include/main_header.php");

?>

 <div class="main" id="otherbody">

    <div class="home_main-content">

        <div class="home_panel-heading">

            <h3 class="home_panel-title">Check Website SEO Score</h3>

            <p class="home_panel-subtitle">Data 100% validated via official API.</p>

        </div>
        
        <div class="main_search">

            <form action="" method="GET">

                 <div class="input-group">

                     <input type="text" name ="domain" placeholder="Scan a website now. Example : Cragglist.com"><input type="submit" name="submit" value="Fetch" />

                 </div>

            </form>

        </div>

    </div>
     
    
    <div class="main_home_recent">
        
        <ul>
      
            <?php

            $latest_query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `domain` FROM `domain` WHERE `status` = '200' GROUP BY `domain` ORDER BY `id` DESC LIMIT 0, 8");

            $latest_count = array_pop(mysqli_fetch_row(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));

            while($row = mysqli_fetch_object($latest_query)) {
                
                $main_query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `domain`, `id`, `array`  FROM `meta` WHERE `domain` = '".$row->domain."' ORDER BY `id` DESC LIMIT 0, 1");
                
                $main_count = array_pop(mysqli_fetch_row(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));
                 
                if($main_count > 0) {
                    
                    $row = mysqli_fetch_object($main_query);

                    $array = unserialize($row->array); ?><li class="pure-u-1-4 mobile_display">

                        <div class="main_recent">

                            <a href="<?=$setting['site_url']?>/?domain=<?=$row->domain?>">

                            <?php

                            $main_color = "#eeeeee";

                            if(!space($array['theme-color'])) {

                                $main_color = $array['theme-color'];

                            }

                            if(!space($array['og:image'])) { 

                                $main_parse = parse_url($array["url"]);

                                $array['og:image'] = fix_url($array['og:image'], $main_parse) 

                                ?><div class="home_test_bentch" style="background-image: url(<?=$array['og:image']?>);"></div><?php

                            }
                            else {

                                ?><div class="main_test_bench_home"><?=substr($row->domain, 0, 2)?></div><?php

                            }

                            ?>

                            </a>

                        </div>

                    </li><?php
                    
                }

            }

            ?>
            
        </ul>
            
    </div>

    <?php

    require_once('include/footer.php');

    ?>
    
</div>
					
</body>

</html>
