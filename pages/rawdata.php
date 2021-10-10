<?php

if(intval($count) > 0) {  
    
    $query = mysqli_query($setting['Lid'],"SELECT SQL_CALC_FOUND_ROWS `domain`, `id`, `array`  FROM `meta` WHERE `domain` = '".$domain."' LIMIT 0, 1");

    $count = array_pop(mysqli_fetch_row(mysqli_query($setting['Lid'],"SELECT FOUND_ROWS()")));
    
    $row = mysqli_fetch_object($query);

    $array = unserialize($row->array);
    
    print("<pre>".print_r($array,true)."</pre>");
    
}

?>
