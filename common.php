<?php

foreach($_POST as $item => $key){

	$N_POST[$item] = mysqli($key);

}

$_POST = array_replace($_POST, $N_POST);

foreach($_GET as $item => $key){

	$N_GET[$item] = mysqli($key);

}

$_GET = array_replace($_GET, $N_GET);

$ddos = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"], "SELECT COUNT(`id`) FROM `views` WHERE `timestamp` > (NOW() - INTERVAL 30 SECOND) AND `ip` = '".getRealIpAddr()."'")));

if ($ddos > 200) {

	header("location: ".$setting["site_url"]."/ddos");

}

function random_color_part() {

	return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);

}

function random_color() {

    return random_color_part() . random_color_part() . random_color_part();

}

function DateRangeArray($from, $to) {

    $array=array();

    $from = mktime(1, 0, 0, substr($from, 5, 2), substr($from, 8, 2), substr($from, 0, 4));
    
    $to = mktime(1, 0, 0, substr($to, 5, 2), substr($to, 8, 2), substr($to, 0, 4));

    if ($to >= $from) {

        array_push($array, date('Y-m-d',$from));

        while ($from < $to) {

            $from+= 86400;
            
            array_push($array, date('Y-m-d',$from));

        }

    }

    return $array;

}

function closetags($html) {

    $html_new = $html;

    preg_match_all ( "#<([a-z]+)( .*)?(?!/)>#iU", $html, $result1);
    
    preg_match_all ( "#</([a-z]+)>#iU", $html, $result2);

    $results_start = $result1[1];
    
    $results_end = $result2[1];

    foreach($results_start AS $startag){

        if(!in_array($startag, $results_end)){

            $html_new = str_replace('<'.$startag.'>', '', $html_new);

        }

    }

    foreach($results_end AS $endtag){

        if(!in_array($endtag, $results_start)){

            $html_new = str_replace('</'.$endtag.'>', '', $html_new);

        }

    }

    return $html_new;

}

function convert_encoding($string, $encoding = 'UTF-8') {

    $string = html_entity_decode($string);

	$array = utf8_split($string);

	foreach($array as $key => $main) {

		if(utf8_decode($main) != $main) {

			$main = mb_convert_encoding($main, 'HTML-ENTITIES', $encoding);

		}

		$array[$key] = $main;

	}

	$string = implode("", $array);

	$string = utf8_trim(utf8_clean($string));

    	return $string;

}

function space($string) {

	$patterns = array('1' => '/\s\s+/i', '2' => '/[^a-zA-Z0-9 -]/', '3' => '/[^[:alpha:]]/', '4' => '/[^a-zA-Z]+/');

	$string = preg_replace($patterns, ' ', $string);

	if($string == "") {

		return true;

	}
	else if(str_ireplace(" ","",preg_replace('/\s+/', '', $string))=="") {

		return true;

	}
	else {

		return false;

	}

}

function extractCommonWords($string, $count){

      $stopWords = array('i','a','about','an','and','are','as','at','be','by','com','de','en','for','from','how','in','is','it','la','of','on','or','that','the','this','to','was','what','when','where','who','will','with','und','the','www');
      $string = preg_replace('/\s\s+/i', '', $string);
    
      $string = trim($string);
    
      $string = preg_replace('/[^a-zA-Z0-9 -]/', '', $string);
    
      $string = strtolower($string); // make it lowercase
    
      preg_match_all('/\b.*?\b/i', $string, $matchWords);
    
      $matchWords = $matchWords[0];

      foreach ( $matchWords as $key=>$item ) {

          	if ( $item == '' || in_array(strtolower($item), $stopWords) || strlen($item) <= 3 ) {

             	unset($matchWords[$key]);

          	}

      }

      $wordCountArr = array();

      if ( is_array($matchWords) ) {

          	foreach ( $matchWords as $key => $val ) {

     			$val = strtolower($val);

              	if ( isset($wordCountArr[$val]) ) {

               		$wordCountArr[$val]++;

              	}
			else {

          			$wordCountArr[$val] = 1;

           	}

       	}

	}

	arsort($wordCountArr);

	$wordCountArr = array_slice($wordCountArr, 0, $count);

	return $wordCountArr;

}

function mime_content_type_image($filename) {

	global $setting;

	$headers = get_headers($filename, 1);

	if (array_search($headers["Content-Type"], $setting["image_types"])) {

 		return $headers["Content-Type"];

	}
	else if (function_exists('finfo_open')) {

		$finfo = finfo_open(FILEINFO_MIME);
        
		$mimetype = finfo_file($finfo, $filename);
        
		finfo_close($finfo);

		return $mimetype;

	}

}

function mime_content_types($filename) {

	global $setting;

	$headers = get_headers($filename, 1);

	if (array_search($headers["Content-Type"], $setting["game_types"])) {

 		return $headers["Content-Type"];

	}
	else if (function_exists('finfo_open')) {

		$finfo = finfo_open(FILEINFO_MIME);
        
		$mimetype = finfo_file($finfo, $filename);
        
		finfo_close($finfo);

		return $mimetype;

	}

}

function getthumbimage($src, $tmp_src, $size, $unlink = 1, $custom = array()) {

	global $setting;

	$file = file_get_contents($tmp_src);

	$array = array("gif", "jpeg", "png", "jpg");

	$type = pathinfo($src, PATHINFO_EXTENSION);
    
    if(!space($type)) {
        
         $type = strtolower(substr($src, strrpos($src, '.') + 1));

         if (!in_array($type, $array)) {
             
             $type = 'png';

         }
        
    }

	if (array_search(strtolower($type), $array)) {

		$setting["alp"] = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

		$salt = randomurl(
		randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"])
		).randomurl(
		randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"])
		).randomurl(
		randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"])
		).randomurl(
		randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"])
		).randomurl(
		randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"]).randomurl($setting["alp"])
		).date('mdYhis', time());

		$setting = array_replace($setting, $custom);
        
		$type = array_keys($setting["image_types"], $type);

		$imgt = "Image".$setting["typeset"];

 		$dir = $setting["dir_sub"].$setting["image_dir"].$salt.".".$setting["typeset"];
        
		$tempdir = $setting["dir_sub"].$setting["temp_dir"].$salt.".".$setting["typeset"];

		$thumbdir = $setting["dir_sub"].$setting["thumb_dir"].$salt.".".$setting["typeset"];

		$file = file_get_contents($tmp_src);

		$old = imagecreatefromstring($file);

		$imgt($old, $tempdir);

		$sizes = getimagesize($tempdir);

		$width = $sizes[0];
        
		$height = $sizes[1];

		if ($width > $setting["getmaxwidth"] && $height > $setting["getmaxheight"]) {

			if ($width > $height && $height > $setting["maxheight"]) {

				$heightn = intval($height * $setting["maxwidth"] / $width);
                
				$widthn = $setting["maxwidth"];

			}
			else if ($width > $maxwidth) {

				$widthn = intval($width * $setting["maxheight"] / $height);
                
				$heightn = $setting["maxheight"];

			}
			else {

				$widthn = $width;
                
				$heightn = $height;

			}

			if($heightn < $height && $widthn < $width) {

				$height = $heightn;
                
				$width = $widthn;

			}

			$imgt(image_resize($width, $height, $old, $sizes, $imgt), $thumbdir);

		}
		else {

			$imgt(image_resize($width, $height, $old, $sizes, $imgt), $thumbdir);

		}

		$array['thumb'] = $salt.".".$setting["typeset"];
        
		$array['width'] = $width;
        
		$array['height'] = $height;

		if($unlink == 1) {

			unlink($tempdir);

		}

	}
	else {

		$array['error'][] = "The following file is unsupported, please try another image.";

	}

	return $array;

}

function image_resize($width, $height, $old, $sizes, $imgt) {

	$thumbt = imagecreatetruecolor($width, $height);

	$backgroundColor = imagecolorallocate($thumbt , 255, 255, 255);

	imagefill($thumbt, 0, 0, $backgroundColor);

	imagecopyresized($thumbt, $old, 0, 0, 0, 0, $width, $height, $sizes[0], $sizes[1]);

	ob_start();

	$imgt($thumbt);

	$thumb = ob_get_contents();

	ob_end_clean();

	imagedestroy($thumbt);

	return imagecreatefromstring($thumb);

}

function getRealIpAddr() {

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {

		$ip=$_SERVER['HTTP_CLIENT_IP'];

	}

	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {

		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];

	}
	else {

		$ip=$_SERVER['REMOTE_ADDR'];

	}

	return $ip;
}

function htmlstring( $html ) {

	$replace=array('<','>');
    
	$to=array('&lt;','&gt;');
    
	return htmlspecialchars(str_ireplace($replace,$to, $html));

}

function bd_nice_number($n) {

	// first strip any formatting;
	$n = (0+str_replace(",","",$n));

	// is this a number?
	if(!is_numeric($n)) return false;

	// now filter it;
	if($n>1000000000000) return round(($n/1000000000000),1).' trillion';

	else if($n>1000000000) return round(($n/1000000000),1).' billion';

	else if($n>1000000) return round(($n/1000000),1).' million';

	else if($n>1000) return round(($n/1000),1).' thousand';

	return number_format($n);

}

function limit_text( $string, $limiter ) {

	$count = str_word_count($string, 2);

	$key = array_keys($count);

	$length = strlen($string);

	$word_count = str_word_count($string);

	$ratio = $length/$word_count;

	if($ratio != 0) {

		$new_word_count = $length/$ratio;

		$difference = $word_count/$new_word_count;

		$limiters = round($difference * $limiter);

	}

	if($limiters < $limiter) {

		$limiter = $limiters;

	}

	if (count($count) > $limiter) {

		$string = trim(substr($string, 0, $key[$limiter])).'&#8230;';

	}

	return $string;

}

function limiter($string, $limit, $arrays) {

	foreach($arrays as $array) {

		$string = implode($array,array_splice(explode($array,$string),0,$limit));

	}

	return $string;
}

function mysqli( $string ) {

	global $setting;

	return mysqli_real_escape_string($setting["Lid"],$string);

}

function username( $id ) {

	global $setting;

	$query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `username` FROM `users` WHERE `id`='".$id."'");
    
	$count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

	if ($count > 0) {

		$row = mysqli_fetch_object($query);
		return $row->username;

	}

}

function getUser($id = 0) {

	global $setting;

	if($id > 0) {

		$query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `users` WHERE `id`='".$id."'");

		$count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

		if ($count > 0) {

			$user = mysqli_fetch_object($query);

		}

		if($user->color == "") {

			$user->color = random_color();

			mysqli_query($setting["Lid"],"UPDATE `users` SET `color` = '".$user->color."' WHERE `users`.`id` = '".$user->id."';");

		}

	}
	else if (isset($_COOKIE["username"])) {

		$password = preg_replace("/[^a-z,A-Z,0-9]/", "", $_COOKIE['code']);

		$query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS * FROM `users` WHERE `id`='".intval($_COOKIE['userid'])."' AND `password`='".mysqli($password)."'");

		$count=array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

		if ($count > 0) {

			$user = mysqli_fetch_object($query);

			if ($user->banned == 1) {

				$user->error='<div class="module_notice">Your account has been blocked for violating one of our term of use and for misusing our site.</div>';
                
				$user->login_status = 0;
                
				$user->admin = 0;

			}
			else if ($user->active == 0) {

				$user->error='<div class="module_notice">Your account is inactive, please validate your account via the validation email.</div>';
                
				$user->login_status = 0;
                
				$user->admin = 0;

			}
			else {

				$user->login_status = 1;


			}

		}
		else {

			$user->login_status = 0;
            
			$user->admin = 0;

		}

	}
	else {

		$user->login_status = 0;
        
		$user->admin = 0;

	}

	if($user->icon == "") {

		$user->icon = $setting["user_icon"];

	}

	return $user;

}

function randomurl( $alphabet ) {

	$pass = array();

	$alphaLength = strlen($alphabet) - 1;

	for ($i = 0; $i < 8; $i++) {

		$new = rand(0, $alphaLength);

		$pass[] = $alphabet[$new];

	}

	return implode($pass);

}

function log_user($user) {

	global $setting;

	$log_user="0";

	if($user->login_status == 1) {

		$log_user=$user->id;

	}

	mysqli_query($setting["Lid"],'INSERT INTO `views`(`ip`, `user_agent`, `url`, `user`) VALUES ("'.getRealIpAddr().'","'.$_SERVER['HTTP_USER_AGENT'].'","'.$_SERVER['REQUEST_URI'].'","'.$log_user.'")');

}

function time_elapsed_string( $time ) {

	$etime = time() - $time;

	if ($etime < 1) {

		return '0 seconds';

	}

	$array = array( 12 * 30 * 24 * 60 * 60  =>  'year',
    	30 * 24 * 60 * 60       =>  'month',
    	24 * 60 * 60            =>  'day',
    	60 * 60                 =>  'hour',
    	60                      =>  'minute',
    	1                       =>  'second'
    	);

	foreach ($array as $secs => $string) {

		$dtime = $etime / $secs;

		if ($dtime >= 1) {

			$rtime = round($dtime);
			return $rtime . ' ' . $string . ($rtime > 1 ? 's' : '') . ' ago';

		}

	}

}

function country() {

	$array['AF'] = 'Afghanistan';
    
	$array['AX'] = 'Aland Islands';
    
	$array['AL'] = 'Albania';
    
	$array['DZ'] = 'Algeria';
    
	$array['AS'] = 'American Samoa';
    
	$array['AD'] = 'Andorra';
    
	$array['AO'] = 'Angola';
    
	$array['AI'] = 'Anguilla';
    
	$array['AQ'] = 'Antarctica';
    
	$array['AG'] = 'Antigua and Barbuda';
    
	$array['AR'] = 'Argentina';
    
	$array['AM'] = 'Armenia';
    
	$array['AW'] = 'Aruba';
    
	$array['AU'] = 'Australia';
    
	$array['AT'] = 'Austria';
    
	$array['AZ'] = 'Azerbaijan';
    
	$array['BS'] = 'Bahamas';
    
	$array['BH'] = 'Bahrain';
    
	$array['BD'] = 'Bangladesh';
    
	$array['BB'] = 'Barbados';
    
	$array['BY'] = 'Belarus';
    
	$array['BE'] = 'Belgium';
    
	$array['BZ'] = 'Belize';
    
	$array['BJ'] = 'Benin';
    
	$array['BM'] = 'Bermuda';
    
	$array['BT'] = 'Bhutan';
    
	$array['BO'] = 'Bolivia';
    
	$array['BA'] = 'Bosnia and Herzegovina';
    
	$array['BW'] = 'Botswana';
    
	$array['BV'] = 'Bouvet Island';
    
	$array['BR'] = 'Brazil';
    
	$array['IO'] = 'British Indian Ocean Territory';
    
	$array['BN'] = 'Brunei Darussalam';
    
	$array['BG'] = 'Bulgaria';
    
	$array['BF'] = 'Burkina Faso';
    
	$array['BI'] = 'Burundi';
    
	$array['KH'] = 'Cambodia';
    
	$array['CM'] = 'Cameroon';
    
	$array['CA'] = 'Canada';
    
	$array['CV'] = 'Cape Verde';
    
	$array['KY'] = 'Cayman Islands';
    
	$array['CF'] = 'Central African Republic';
    
	$array['TD'] = 'Chad';
    
	$array['CL'] = 'Chile';
    
	$array['CN'] = 'China';
    
	$array['CX'] = 'Christmas Island';
    
	$array['CC'] = 'Cocos (Keeling) Islands';
    
	$array['CO'] = 'Colombia';
    
	$array['KM'] = 'Comoros';
    
	$array['CG'] = 'Congo';
    
	$array['CD'] = 'Congo, Democratic Republic of the';
    
	$array['CK'] = 'Cook Islands';
    
	$array['CR'] = 'Costa Rica';
    
	$array['CI'] = 'Cote D\'ivoire';
    
	$array['HR'] = 'Croatia';
    
	$array['CU'] = 'Cuba';
    
	$array['CY'] = 'Cyprus';
    
	$array['CZ'] = 'Czech Republic';
    
	$array['DK'] = 'Denmark';
    
	$array['DJ'] = 'Djibouti';
    
	$array['DM'] = 'Dominica';
    
	$array['DO'] = 'Dominican Republic';
    
	$array['EC'] = 'Ecuador';
    
	$array['EG'] = 'Egypt';
    
	$array['SV'] = 'El Salvador';
    
	$array['GQ'] = 'Equatorial Guinea';
    
	$array['ER'] = 'Eritrea';
    
	$array['EE'] = 'Estonia';
    
	$array['ET'] = 'Ethiopia';
    
	$array['FK'] = 'Falkland Islands (Malvinas)';
    
	$array['FO'] = 'Faroe Islands';
    
	$array['FJ'] = 'Fiji';
    
	$array['FI'] = 'Finland';
    
	$array['FR'] = 'France';
    
	$array['GF'] = 'French Guiana';
    
	$array['PF'] = 'French Polynesia';
    
	$array['TF'] = 'French Southern Territories';
    
	$array['GA'] = 'Gabon';
    
	$array['GM'] = 'Gambia';
    
	$array['GE'] = 'Georgia';
    
	$array['DE'] = 'Germany';
    
	$array['GH'] = 'Ghana';
    
	$array['GI'] = 'Gibraltar';
    
	$array['GR'] = 'Greece';
    
	$array['GL'] = 'Greenland';
    
	$array['GD'] = 'Grenada';
    
	$array['GP'] = 'Guadeloupe';
    
	$array['GU'] = 'Guam';
    
	$array['GT'] = 'Guatemala';
    
	$array['GN'] = 'Guinea';
    
	$array['GW'] = 'Guinea-Bissau';
    
	$array['GY'] = 'Guyana';
    
	$array['HT'] = 'Haiti';
    
	$array['HM'] = 'Heard Island and Mcdonald Islands';
    
	$array['HN'] = 'Honduras';
    
	$array['HK'] = 'Hong Kong';
    
	$array['HU'] = 'Hungary';
    
	$array['IS'] = 'Iceland';
    
	$array['IN'] = 'India';
    
	$array['ID'] = 'Indonesia';
    
	$array['IR'] = 'Iran, Islamic Republic of';
    
	$array['IQ'] = 'Iraq';
    
	$array['IE'] = 'Ireland';
    
	$array['IL'] = 'Israel';
    
	$array['IT'] = 'Italy';
    
	$array['JM'] = 'Jamaica';
    
	$array['JP'] = 'Japan';
    
	$array['JO'] = 'Jordan';
    
	$array['KZ'] = 'Kazakhstan';
    
	$array['KE'] = 'Kenya';
    
	$array['KI'] = 'Kiribati';
    
	$array['KP'] = 'Korea, Democratic People\'s Republic of';
    
	$array['KR'] = 'Korea, Republic of';
    
	$array['KW'] = 'Kuwait';
    
	$array['KG'] = 'Kyrgyzstan';
    
	$array['LA'] = 'Lao People\'s Democratic Republic';
    
	$array['LV'] = 'Latvia';
    
	$array['LB'] = 'Lebanon';
    
	$array['LS'] = 'Lesotho';
    
	$array['LR'] = 'Liberia';
    
	$array['LY'] = 'Libyan Arab Jamahiriya';
    
	$array['LI'] = 'Liechtenstein';
    
	$array['LT'] = 'Lithuania';
    
	$array['LU'] = 'Luxembourg';
    
	$array['MK'] = 'Macedonia, the Former Yugoslav Republic of';
    
	$array['MG'] = 'Madagascar';
    
	$array['MW'] = 'Malawi';
    
	$array['MY'] = 'Malaysia';
    
	$array['MV'] = 'Maldives';
    
	$array['ML'] = 'Mali';
    
	$array['MT'] = 'Malta';
    
	$array['MH'] = 'Marshall Islands';
    
	$array['MQ'] = 'Martinique';
    
	$array['MR'] = 'Mauritania';
    
	$array['MU'] = 'Mauritius';
    
	$array['YT'] = 'Mayotte';
    
	$array['MX'] = 'Mexico';
    
	$array['FM'] = 'Micronesia';
    
	$array['MD'] = 'Moldova';
    
	$array['MC'] = 'Monaco';
    
	$array['MN'] = 'Mongolia';
    
	$array['ME'] = 'Montenegro';
    
	$array['MS'] = 'Montserrat';
    
	$array['MA'] = 'Morocco';
    
	$array['MO'] = 'Mozambique';
    
	$array['MZ'] = 'Mozambique';
    
	$array['MM'] = 'Myanmar';
    
	$array['NA'] = 'Namibia';
    
	$array['NR'] = 'Nauru';
    
	$array['NP'] = 'Nepal';
    
	$array['NL'] = 'Netherlands';
    
	$array['AN'] = 'Netherlands Antilles';
    
	$array['NT'] = 'Neutral Zone';
    
	$array['NC'] = 'New Caledonia';
    
	$array['NZ'] = 'New Zealand';
    
	$array['NI'] = 'Nicaragua';
    
	$array['NE'] = 'Niger';
    
	$array['NG'] = 'Nigeria';
    
	$array['NU'] = 'Niue';
    
	$array['NF'] = 'Norfolk Island';
    
	$array['MP'] = 'Northern Mariana Islands';
    
	$array['NO'] = 'Norway';
    
	$array['OM'] = 'Oman';
    
	$array['PK'] = 'Pakistan';
    
	$array['PW'] = 'Palau';
    
	$array['PS'] = 'Palestinian Territory, Occupied';
    
	$array['PA'] = 'Panama';
    
	$array['PG'] = 'Papua New Guinea';
    
	$array['PY'] = 'Paraguay';
    
	$array['PE'] = 'Peru';
    
	$array['PH'] = 'Philippines';
    
	$array['PN'] = 'Pitcairn';
    
	$array['PL'] = 'Poland';
    
	$array['PT'] = 'Portugal';
    
	$array['PR'] = 'Puerto Rico';
    
	$array['QA'] = 'Qatar';
    
	$array['RE'] = 'Reunion';
    
	$array['RO'] = 'Romania';
    
	$array['RU'] = 'Russian Federation';
    
	$array['RW'] = 'Rwanda';
    
	$array['SH'] = 'Saint Helena';
    
	$array['KN'] = 'Saint Kitts and Nevis';
    
	$array['LC'] = 'Saint Lucia';
    
	$array['PM'] = 'Saint Pierre and Miquelon';
    
	$array['VC'] = 'Saint Vincent and the Grenadines';
    
	$array['WS'] = 'Samoa';
    
	$array['SM'] = 'San Marino';
    
	$array['ST'] = 'Sao Tome and Principe';
    
	$array['SA'] = 'Saudi Arabia';
    
	$array['SN'] = 'Senegal';
    
	$array['RS'] = 'Serbia';
    
	$array['CS'] = 'Serbia and Montenegro';
    
	$array['SY'] = 'Seychelles';
    
	$array['SC'] = 'Seychelles';
    
	$array['SL'] = 'Sierra Leone';
    
	$array['SG'] = 'Singapore';
    
	$array['SK'] = 'Slovakia';
    
	$array['SI'] = 'Slovenia';
    
	$array['SB'] = 'Solomon Islands';
    
	$array['SO'] = 'Somalia';
    
	$array['ZA'] = 'South Africa';
    
	$array['GS'] = 'South Georgia and the South Sandwich Islands';
    
	$array['ES'] = 'Spain';
    
	$array['LK'] = 'Sri Lanka';
    
	$array['SD'] = 'Sudan';
    
	$array['SR'] = 'Suriname';
    
	$array['SJ'] = 'Svalbard and Jan Mayen Islands';
    
	$array['SZ'] = 'Swaziland';
    
	$array['SE'] = 'Sweden';
    
	$array['CH'] = 'Switzerland';
    
	$array['TW'] = 'Taiwan';
    
	$array['TJ'] = 'Tajikistan';
    
	$array['TZ'] = 'Tanzania, United Republic of';
    
	$array['TH'] = 'Thailand';
    
	$array['TL'] = 'Timor-Leste';
    
	$array['TG'] = 'Togo';
    
	$array['TK'] = 'Tokelau';
    
	$array['TO'] = 'Tonga';
    
	$array['TT'] = 'Trinidad and Tobago';
    
	$array['TN'] = 'Tunisia';
    
	$array['TR'] = 'Turkey';
    
	$array['TM'] = 'Turkmenistan';
    
	$array['TC'] = 'Turks and Caicos Islands';
    
	$array['TV'] = 'Tuvalu';
    
	$array['UG'] = 'Uganda';
    
	$array['UA'] = 'Ukraine';
    
	$array['AE'] = 'United Arab Emirates';
    
	$array['GB'] = 'United Kingdom';
    
	$array['UK'] = 'United Kingdom';
    
	$array['US'] = 'United States';
    
	$array['UY'] = 'Uruguay';
    
	$array['UZ'] = 'Uzbekistan';
    
	$array['VU'] = 'Vanuatu';
    
	$array['VA'] = 'Vatican City State';
    
	$array['VE'] = 'Venezuela';
    
	$array['VN'] = 'Viet Nam';
    
	$array['VG'] = 'Virgin Islands, British';
    
	$array['VI'] = 'Virgin Islands, U.S.';
    
	$array['WF'] = 'Wallis and Futuna Islands';
    
	$array['EH'] = 'Western Sahara';
    
	$array['YE'] = 'Yemen';
    
	$array['YU'] = 'Yugoslavia';
    
	$array['ZM'] = 'Zambia';
    
	$array['ZW'] = 'Zimbabwe';

	return $array;

}

function validate_email($email){

   	if(preg_match("/^[_.\da-z-]+@[a-z\d][a-z\d-]+\.+[a-z]{2,6}/i",$email)){

      	if(checkdnsrr(array_pop(explode("@",$email)),"MX")){

			return true;

		}
		else{

       	 	return false;

      	}

   	}
	else {

      	return false;

   	}

}

function email_system($email, $subject, $message) {

	global $setting;

	$body .= "<html>
	<head>
	<title>".$subject."</title>
	</head>
	<body>";

	$body .= $message;

	$body .= "</body></html>";

	$headers = "MIME-Version: 1.0" . "\r\n";

	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	//$headers .= 'Cc: myboss@example.com' . "\r\n";

   	$headers .= 'From: <'.$setting["no_reply"].'>' . "\r\n";

	mail($email, $subject, $body, $headers);

}

function main_domain($url) {

	global $extension;

	$parse = parse_url($url);

	$url = $parse['host'];

	if (substr($url,0 , 4) == 'www.') {

		$url = substr($url, 4);

	}

	$domain = explode(".", $url);

	$count = count($domain);

	$top_level_domain = ".".$domain[$count-1];

	$second_level_domain = ".".$domain[$count-2];

	$domain_extension = $second_level_domain.$top_level_domain;

	if (in_array($domain_extension, $extension)) {

		if($domain[$count-4] != "") {

			$sub_domain = $domain[$count-4].".";

		}

		$main_host = $domain[$count-3];

		$top_level_domain = $domain_extension;

	}
	else if (in_array($top_level_domain, $extension)) {

		if($domain[$count-3] != "") {

			$sub_domain = $domain[$count-3].".";

		}

		$main_host = $second_level_domain;

	}
	else if($domain[$count-4] != "") {

		$sub_domain = $domain[$count-4];

		$main_host = ".".$domain[$count-3];

		$top_level_domain = $domain_extension;

	}
	else if($domain[$count-3] != "") {

		$sub_domain = $domain[$count-3];

		$main_host = $second_level_domain;

	}
	else {

		$main_host = $second_level_domain;

	}

	if(substr($main_host, -1) == "/") {

		$main_host = substr($main_host, 0 , -1);

	}

	if(substr($main_host, 0, 1) == ".") {

		$main_host = substr($main_host, 1);

	}

	$array['url'] = $sub_domain.$main_host.$top_level_domain;

	if($array['url'] == "") {

		$array['url'] = $url;

	}

	$array['sub'] = $sub_domain;

	$array['main'] = $main_host;

	$array['extension'] = $top_level_domain;

	return $array;

}

function redirection($url) {

	$headers = get_headers($url);

	foreach($headers as $header) {

		if (preg_match('/^Location: (.+?)$/m', $header, $match)) {

			$parse = parse_url($url);

			$url=trim($match[1]);

			if (substr($match[1], 0, 1) == "/") {

				$url=$parse['scheme']."://".$parse['host'].trim($match[1]);

			}

		}

	}

	return $url;

}

function getpattern(&$patterns, &$array, $content) {

	$returnarray = array();

	foreach($patterns as $pattern) {

		preg_match_all($pattern, $array['response'], $matchs);

		foreach($matchs['1'] as $key => $match) {

			preg_match("/<([a-z0-9]+)[^>]*?>/i", $matchs['0'][$key], $other);

			$return = indextext($match);

			if(!space($return)) {

				$returnarray[$other['1']][] = $return;

			}

		}

	}

	return $returnarray;

}

function removeLastDir($path, $level) {
	
	if(substr($path, 0, 1) == '/') {

		$path = substr($path,0, -1);

	}

	$paths = array_filter(explode("/", $path));
    
    for($i = $level; $i > 0; $i--) {
		
		$new_path = count($paths) - ($i - 1);
        
        $path = preg_replace("/\/".$paths[$new_path]."/si", '', $path);
        
    }
    return $path;
}

function fix_url($match, $parse) {
    
    $path = pathinfo($parse['path']);
    
    $parse['path'] = $path['dirname'];

	if(!empty($match)) {
        
        if(substr_count($match, "../") > 0 || substr_count($match, "./") > 0) {
            
            if(substr_count($match, "../") > 0) {
                
                $level = substr_count($match, "../");
                
            }
            else {

                $level = substr_count($match, "./");
                
            }

            $new_path = removeLastDir($parse['path'], $level);
            
            $array = array("../", "./");
            
            $match = str_replace($array, "", $match);
            
            if(substr($match, 0, 1) == '/') {

				$match = substr($match, 1);

			}
            
            $match = $new_path."/".$match;
            
            
        }
        else if(!empty($match) && strpos($match,"www.") != 'FALSE' && strpos($match,"http://") != 'FALSE' && strpos($match,"https://") != 'FALSE' && strpos($match,"//") != 'FALSE' && strpos($match,"://") != 'FALSE' && strpos($match,"mailto") != 'FALSE' && strpos($match,"data") != 'FALSE' && !space($parse['path'])) {
            
  			if(substr($match, 0, 1) == '/') {

				$match = substr($match, 1);

			}
                
            $match = $parse['path']."/".$match;
            
        }

		if(!empty($match) && strpos($match,"www.") != 'FALSE' && strpos($match,"http://") != 'FALSE' && strpos($match,"https://") != 'FALSE' && strpos($match,"//") != 'FALSE' && strpos($match,"://") != 'FALSE' && strpos($match,"mailto") != 'FALSE' && strpos($match,"data") != 'FALSE') {

			if(substr($match, 0, 1) == '/') {

				$match = substr($match, 1);

			}

			$match = $parse['scheme']."://".$parse['host']."/".$match;

		}
		else if(!empty($match) && strpos($match,"mailto") != 'FALSE' && strpos($match,"data") != 'FALSE' && strpos($match,"http://") != 'FALSE' && strpos($match,"https://") != 'FALSE') {

			if(substr($match, 0, 1) == ':') {

				$match = substr($match, 1);

			}

			if(substr($match, 0, 2) == '//') {
                
                $match = substr($match, 2);

			}
            
            if(substr($match, 0, strlen($parse['host'])) == $parse['host']) {
                    
                $match = $parse['scheme']."://".$match;
                    
            }
            else {
                    
                $match = "http://".$match;
                    
            }
			
		}
            
	}

	return $match;

}

function url_info($url, $type = 0, $username = "", $password = "", $request_header = 0, $post = 0, $string = "") {

	$curl = curl_init();

	global $setting, $html_to_array;

	curl_setopt($curl, CURLOPT_BINARYTRANSFER,1);

	curl_setopt($curl, CURLOPT_AUTOREFERER, false);

	curl_setopt($curl, CURLOPT_REFERER, 'http://google.com');

	if($type == 2) {

		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

		curl_setopt($curl, CURLOPT_USERPWD,  "".$username.":".$password."");

	}

	curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

	curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');

	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);

	curl_setopt($curl, CURLOPT_URL, $url);
    
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

	curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

	curl_setopt($curl, CURLOPT_VERBOSE, 1);
    
    curl_setopt($curl, CURLOPT_HEADER, 1);

	curl_setopt($curl, CURLOPT_USERAGENT, ''.$setting["robot"].'');

	curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);

	curl_setopt($curl, CURLOPT_TIMEOUT, 10);
    
    $cookie_file = "cookie.txt";
    
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);

    curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie_file);

    curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie_file);

	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
    if($post == 1) {
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, $string);

    }

	$text = curl_exec($curl);
    
    $array['curl_info'] = curl_getinfo($curl);
    
    $array['response_header'] = substr($text, 0, $array['curl_info']['header_size']);

    $array['response'] = substr($text, $array['curl_info']['header_size']);

	$array['status'] = mysqli(curl_getinfo($curl, CURLINFO_HTTP_CODE));

	$array['error'] = mysqli(curl_error($curl));
    
	if (curl_getinfo($curl, CURLINFO_EFFECTIVE_URL) != $url) {

		$url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);

	}
    
    if($request_header == 1) {
    
        $array = curlheader($array);
        
    }

	$array['url'] = mysqli($url);

	if($type == 1) {

		$array['response'] = convert_encoding($array['response']);
        
        $matches = array('@<script[^>]*?>.*?<\/script>@si', '@<style[^>]*?>.*?<\/style>@si', '@<noscript[^>]*?>.*?<\/noscript>@si');

        $array['response'] = preg_replace($matches, ' ', $array['response']);

		$html_to_array->loadHTML($array['response']);

		$html_array_path = new \DOMXpath($html_to_array);

	}
    
	if($array['response'] === false) {

		trigger_error(curl_error($curl));

	}
    
    curl_close($curl);

	return $array;

}

function site_info(&$array) {
	
	global $setting, $html_to_array, $html_array_path;

	if($array['status'] == 200) {

		$meta = array('1' => 'charset', '2' => 'itemprop', '3' => 'http-equiv', '4'=> 'property', '5' => 'name');

      	$array['content'] = (string) mysqli(indextext($array['response']));

		//$array['full_text'] = mysqli(indextext(indexpartialtext($array['response'])));
        
        $nodes = $html_to_array->getElementsByTagName('title');
       
        $array['title'] = (string) $nodes->item(0)->nodeValue;

		$matchs = $html_to_array->getElementsByTagName('meta');

		foreach($matchs as $match) {

			foreach ($meta as $key => $names) {

				$name = mysqli($match->getAttribute($names));

				if (($name != null) && (!space($name))) {

					$content = (string) mysqli($match->getAttribute('content'));

					$array[$name] = (string) mysqli(indexpartialtext(indextext($content)));

				}

           	}
				
		}

		$titleheaders = content($array);

		$array = array_merge($titleheaders, $array);

		if($setting['getkeywords'] == 1) {

			$array['density'] = (string) mysqli(implode(',', array_keys(extractCommonWords($array['content'], 30))));
					
		}

	}

	$array = array_filter($array);

	array_multisort($array, SORT_STRING);

	return $array;

}

function content($array) {

	global $html_to_array;

	$main_parse = parse_url($array["url"]);
	
	$patterns = array("title", "h1", "h2", "h3", "h4", "h5", "h6", "h7", "h8", "h9", "p", "span", "div", "img", "a", "article", "code", "pre", "figure", "figcaption");

	foreach($patterns as $key => $pattern) {
        
        $array[$pattern] = array();

		$clone = $html_to_array->cloneNode(True);

		$matchs = $clone->getElementsByTagName($pattern);

		foreach($matchs as $subkey => $match) {

			if($pattern == "img") {

				if(indexpartialtext(indextext($match->getAttribute("src"))) !== "") {

					$array[$pattern][$subkey]["src"] = (string) mysqli(fix_url($match->getAttribute("src"), $main_parse));

					if(indexpartialtext(indextext($match->getAttribute("alt"))) !== "") {

						$array[$pattern][$subkey]["alt"] = (string) mysqli($match->getAttribute("alt"));

					}

					if(indexpartialtext(indextext($match->getAttribute("width"))) !== "") {

						$array[$pattern][$subkey]["width"] = (string) mysqli($match->getAttribute("width"));

					}

					if(indexpartialtext(indextext($match->getAttribute("height"))) !== "") {

						$array[$pattern][$subkey]["height"] = (string) mysqli($match->getAttribute("height"));

					}

				}

				$match->nodeValue = "";

			}
			else if($pattern == "a") {

				if(indexpartialtext(indextext($match->getAttribute("href"))) !== "") {

					$array[$pattern][$subkey]["href"] = mysqli(fix_url($match->getAttribute("href"), $main_parse));

					if(indexpartialtext(indextext($match->nodeValue)) !== "") {

						$array[$pattern][$subkey]["title"] = (string) mysqli(indexpartialtext(indextext($match->nodeValue)));

					}

				}

				$match->nodeValue = "";

			}
			else {

				$content = indexpartialtext(indextext($match->nodeValue));

				if(($content != "") && (!space($content)) && !check_content($patterns, $array, $content) && (str_word_count($content) > 1)) {
                    
					$array[$pattern][] = (string) mysqli($content);

					$match->nodeValue = "";

				}

			}

		}

	}

	return $array;

}

function check_content(&$patterns, &$array, $string) {

	foreach($patterns as $key => $pattern) {

		$arrays = $array[$pattern];

		foreach($arrays as $keys => $match) {

			if($match == $string) {

				return true;

				break;

			}

		}

	}

}

function robots_allowed($url) {

	global $setting;

	$parse = parse_url($url);

	$robots = mysqli($parse['scheme']."://".$parse['host']."/robots.txt");

	$count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT COUNT(`id`) FROM `robot` WHERE `url`='".$robots."'")));

	$query = mysqli_fetch_object(mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `robot`,`url` FROM `robot` WHERE `url`='".$robots."' AND `timestamp` < now() - interval 1 day"));

	$old = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

	if($count == 0 || $old > 0) {

		mysqli_query($setting["Lid"],"DELETE FROM `robot` WHERE `url`='".$robots."'");

		$array = url_info($robots);

		if($array['status'] == 200) {

			mysqli_query($setting["Lid"],"INSERT INTO `robot`(`robot`, `url`) VALUES ('".mysqli($array['response'])."','".$array['url']."')");

		}
		else {

			return true;

		}

	}
	else {

		$array['response'] = $row->robot;

		$array['status'] = 200;

		$array['url'] = $row->url;

	}

	if(check_robot_allowed($array, $parse)) {

		return true;

	}
	else {

		return false;

	}

}

function check_robot_allowed(&$array, $parse) {

	global $setting;

	if($array['status'] == 200) {

		$useragents = explode("User-",$array['response']);

		foreach($useragents as $useragent) {

			preg_match('/^\s*User-agent: (.*)/i', "User-".$useragent, $match);

			if($match[1] == $setting["bot"] || $match[1] == "*") {

				preg_match_all('/Disallow: (.*)/i', $useragent, $matches);

				foreach($matches[1] as $match) {

					$match=str_ireplace(array("?","&","*"), array("[?]","[&]","(.*?)"),$match);

					if($parse['query'] != ""){

						$parse['query'] = "?".$parse['query'];

					}

					preg_match("@".$match."@siU" ,$parse['path'].$parse['query'] ,$match);

					$found[] = $match;

				}

			}

		}

		if($found) {

			$found=array_filter($found);

		}

		if(count($found) > 0) {

			return false;

		}
		else {

			return true;

		}

	}
	else {

		return true;

	}

}

function make_bold($string, $arrays) {

	$strings = explode(' ', $string);

	$alphabet = ".\+*?[^]$(){}=!<>|:-";

	$random = randomurl( $alphabet ).randomurl( $alphabet ).randomurl( $alphabet );

	$random_two = randomurl( $alphabet ).randomurl( $alphabet ).randomurl( $alphabet );

	foreach($arrays as $array) {

		$array = preg_quote($array);

		foreach($strings as $key => $stringe) {

			preg_match("@(".$array.")@siU", $stringe, $found);

			foreach ($found as $replace) {

				$replace = preg_quote($replace);

				if (!strpos($replace,$random_two) && !strpos($replace,$random)) {

					$match = str_ireplace($replace, $random_two.$replace.$random, $stringe);

					$strings[$key] = $match;

				}

			}

		}

	}

	$string = implode(' ', $strings);

	$string = str_ireplace(array($random_two,$random), array("<b>","</b>"), $string);

	return $string;

}

function show_url($url) {

	$showurl = limit_text(limiter($url, 6, array("-","/","=")),6);

	if (substr($showurl,0 , 8) == 'https://' && substr($showurl, 0, 12 ) == 'https://www.') {

		$showurl = substr($showurl, 8);

	}
	else if (substr($showurl,0 , 7) == 'http://' && substr($showurl,0 , 11) == 'http://www.') {

		$showurl = substr($showurl, 7);

	}
	else if (substr($showurl,0 , 7) == 'http://') {

		$showurl = substr($showurl, 7);

	}

	return $showurl;

}

function didyoumean($words) {

	global $setting;

	foreach ($words as $key => $match) {

		$query = mysqli_fetch_object(mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `word` FROM `entries` WHERE `word`='".$match."'"));

		$count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

		if($count == 0) {

			$query = mysqli_query($setting["Lid"],"SELECT SQL_CALC_FOUND_ROWS `word` FROM `entries` WHERE (LEFT(`word`,1)='".substr($match,0,1)."' OR RIGHT(`word`,1)='".substr($match,-1)."') AND soundex(`word`) = soundex('".$match."') AND LENGTH(`word`) = '".strlen($match)."'");

			$count = array_pop(mysqli_fetch_row(mysqli_query($setting["Lid"],"SELECT FOUND_ROWS()")));

			if($count > 0) {

				while ($row = mysqli_fetch_object($query)) {

					$keywords[] = $row->word;

				}

				array_multisort(array_map('strlen', $keywords), $keywords);

				$keywords = array_unique($keywords);

				if(array_search($match, $keywords)) {

					$array[] = $match;

				}
				else {

					$shortest = -1;

					foreach ($keywords as $word) {

						$levenshtein = levenshtein($match, $word);

						if($levenshtein == 0) {

							$closest = $word;

							$shortest = 0;

							break;

						}

						if($levenshtein <= $shortest || $shortest < 0) {

							$closest = $word;

							$shortest = $levenshtein;

						}

					}

					$array[] = $closest;

				}

			}
			else {

				$array[] = $match;

			}


		}
		else {

			$array[] = $match;

		}

	}

	$part = str_ireplace(implode(' ',$words), "", implode(' ',$array));

	if(space($part)) {

		return false;

	}
	else {

		return implode(' ',array_reverse($array));

	}

}

function indextext($string) {

	$array = array('@<script[^>]*?>.*?<\/script>@si',
	'@<noscript[^>]*?>.*?<\/noscript>@si',
	'@<header[^>]*?>.*?<\/header>@si',
	'@<nav[^>]*?>.*?<\/nav>@si',
	'@<style[^>]*?>.*?<\/style>@si',
	'@<link rel[^<>]*?>@si',
	'@<footer[^>]*?>.*?<\/footer>@si',
	'@<![\s\S]*?--[ \t\n\r]*>@si',
	'/&[a-z]{1,6};/',
	'/&nbsp;/',
	'@\s\s+@',
	'@\s+@',
	'@<!--sphider_noindex-->.*?<!--\/sphider_noindex-->@si',
	'@<!--.*?-->@si',
	'/(<|>)\1{2}/si',
	'@<head[^>]*?>.*?<\/head>@si'
	);

	$string = indexpartialtext(preg_replace($array, ' ', $string));

	$string = preg_replace('#<a.*?>(.*?)<\/a>#i', '\1', $string);

	$string = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/si", '<$1$2>', $string);

	$string = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>([a-z0-9']{1,100})<\/([a-z][a-z0-9]*)[^>]*?(\/?)>/is", ' ', $string);

	$string = strip_tags(preg_replace(array('/<\/[\w ]+>/', '/<[\w ]+>/'), '\\0 ', $string));

	$string = preg_replace($array, ' ', $string);

	$string = trim(replaceWhitespace($string));

	return $string;

}

function indexpartialtext($string) {

	$tags = "<a><h1><h2><h3><h4><h5><h6><h7><h8><h9><p><article><b><br><div><ul><ol><li><img><i><em><code><pre><strong><del><figure><figcaption><hr>";

	$string = trim(strip_tags($string, $tags));

	return $string;

}

function replaceWhitespace($string) {

	$array = array("  ", " \t", " \r", " \n", "\t\t", "\t ", "\t\r", "\t\n", "\r\r", "\r ", "\r\t", "\r\n", "\n\n", "\n ", "\n\t", "\n\r");

	foreach ($array as $key => $replacement) {

		$string = str_replace($replacement, $replacement[0], $string);

	}

  return trim($string);

}

function DMS2Decimal($degrees = 0, $minutes = 0, $seconds = 0, $direction = 'n') {

	//https://www.dougv.com/2012/03/converting-latitude-and-longitude-coordinates-between-decimal-and-degrees-minutes-seconds/

   	$d = strtolower($direction);

   	$ok = array('n', 's', 'e', 'w');

   	if(!is_numeric($degrees) || $degrees < 0 || $degrees > 180) {

      	$decimal = false;

   	}
   	else if(!is_numeric($minutes) || $minutes < 0 || $minutes > 59) {

      	$decimal = false;

   	}
   	else if(!is_numeric($seconds) || $seconds < 0 || $seconds > 59) {

      	$decimal = false;

   	}
   	else if(!in_array($d, $ok)) {

      	$decimal = false;

   	}
   	else {

      	$decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

     		if($d == 's' || $d == 'w') {

         		$decimal *= -1;

      	}

   	}

   	return $decimal;

}

function geodata($latlng) {

	$geo = 'http://www.maps.google.com/maps/api/geocode/xml?latlng='.$latlng.'&sensor=true';

	$xml = simplexml_load_file($geo);

	$geodata = array();

	foreach($xml->result->address_component as $component){

		if($component->type=='street_address'){

			$geodata['precise_address'] = $component->long_name;

		}

		if($component->type=='natural_feature'){

			$geodata['natural_feature'] = $component->long_name;

		}

		if($component->type=='airport'){

			$geodata['airport'] = $component->long_name;

		}

		if($component->type=='park'){

			$geodata['park'] = $component->long_name;

		}

		if($component->type=='point_of_interest'){

			$geodata['point_of_interest'] = $component->long_name;

		}

		if($component->type=='premise'){

			$geodata['named_location'] = $component->long_name;

		}

		if($component->type=='street_number'){

			$geodata['house_number'] = $component->long_name;

		}

		if($component->type=='route'){

			$geodata['street'] = $component->long_name;

		}

		if($component->type=='locality'){

			$geodata['town_city'] = $component->long_name;

		}

		if($component->type=='administrative_area_level_3'){

			$geodata['district_region'] = $component->long_name;

		}

		if($component->type=='neighborhood'){

			$geodata['neighborhood'] = $component->long_name;

		}

		if($component->type=='colloquial_area'){

			$geodata['locally_known_as'] = $component->long_name;

		}

		if($component->type=='administrative_area_level_2'){

			$geodata['county_state'] = $component->long_name;

		}

		if($component->type=='postal_code'){

			$geodata['postcode'] = $component->long_name;

		}

		if($component->type=='country'){

			$geodata['country'] = $component->long_name;

		}

	}

	$geodata['google_api_src'] = $geo;

	return $geodata;

}

function map_image($location, $zoom) {

	return '<img src="http://maps.google.com/maps/api/staticmap?center='.$location.'&zoom='.$zoom.'&size=250x150&maptype=roadmap&&sensor=true" width="250" height="150" alt="'.$geodata['formatted_address'].'"/>';

}

//shop functions

function add_to_cart($row) {

	global $setting;

	if(isset($_COOKIE['carts'])) {

		$cookie = $_COOKIE['carts'];

		setcookie("carts", "", time()-60*60*24*100, "/");
        
		setcookie("carts", "", time()-60*60*24*100, "/" ,".".$setting["url"]);

		$cookie = stripslashes($cookie);

		$arrays = json_decode($cookie, true);

	}

	if (!in_array($row->id, $arrays, true)) {

    		$arrays[] = $row->id;

	}

	$json = json_encode($arrays);

	setcookie("carts", $json, time()+60*60*24*100, "/");
    
	setcookie("carts", $json, time()+60*60*24*100, "/", ".".$setting["url"]);

	return $json;

}

function remove_from_cart($row) {

	global $setting;

	if(isset($_COOKIE['carts'])) {

		$cookie = $_COOKIE['carts'];

		setcookie("carts", "", time()-60*60*24*100, "/");
        
		setcookie("carts", "", time()-60*60*24*100, "/" ,".".$setting["url"]);

		$cookie = stripslashes($cookie);

		$arrays = json_decode($cookie, true);

		if(($key = array_search($row->id, $arrays)) !== false) {

    			unset($arrays[$key]);

		}

		$json = json_encode($arrays);

		setcookie("carts", $json, time()+60*60*24*100, "/");
        
		setcookie("carts", $json, time()+60*60*24*100, "/", ".".$setting["url"]);

		return $json;

	}

}

if (!function_exists('money_format')) {

    function money_format($format, $number) {

        $regex  = '/%((?:[\^!\-]|\+|\(|\=.)*)([0-9]+)?'.'(?:#([0-9]+))?(?:\.([0-9]+))?([in%])/';

        if (setlocale(LC_MONETARY, 0) == 'C') {

            setlocale(LC_MONETARY, '');

        }

        $locale = localeconv();

            preg_match_all($regex, $format, $matches, PREG_SET_ORDER);

            foreach ($matches as $fmatch) {

            $value = floatval($number);
                
            $flags = array(

                'fillchar'  => preg_match('/\=(.)/', $fmatch[1], $match) ? $match[1] : ' ',
                
                'nogroup'   => preg_match('/\^/', $fmatch[1]) > 0,
                
                'usesignal' => preg_match('/\+|\(/', $fmatch[1], $match) ? $match[0] : '+',
                
                'nosimbol'  => preg_match('/\!/', $fmatch[1]) > 0,
                
                'isleft'    => preg_match('/\-/', $fmatch[1]) > 0

            );

            $width = trim($fmatch[2]) ? (int)$fmatch[2] : 0;
                
            $left = trim($fmatch[3]) ? (int)$fmatch[3] : 0;
                
            $right = trim($fmatch[4]) ? (int)$fmatch[4] : $locale['int_frac_digits'];
                
            $conversion = $fmatch[5];
                
            $positive = true;

            if ($value < 0) {

                $positive = false;
                
                $value  *= -1;

            }

            $letter = $positive ? 'p' : 'n';

            $prefix = $suffix = $cprefix = $csuffix = $signal = '';

            $signal = $positive ? $locale['positive_sign'] : $locale['negative_sign'];

            switch (true) {

                case $locale["{$letter}_sign_posn"] == 1 && $flags['usesignal'] == '+':

                    $prefix = $signal;

                break;

                case $locale["{$letter}_sign_posn"] == 2 && $flags['usesignal'] == '+':

                    $suffix = $signal;

                break;

                case $locale["{$letter}_sign_posn"] == 3 && $flags['usesignal'] == '+':

                    $cprefix = $signal;

                break;

                case $locale["{$letter}_sign_posn"] == 4 && $flags['usesignal'] == '+':

                    $csuffix = $signal;

                break;

                case $flags['usesignal'] == '(':

                case $locale["{$letter}_sign_posn"] == 0:

                    $prefix = '(';
                    $suffix = ')';

                break;

            }

            if (!$flags['nosimbol']) {

                $currency = $cprefix.($conversion == 'i' ? $locale['int_curr_symbol'] : $locale['currency_symbol']).$csuffix;

            }
            else {

                $currency = '';

            }

            $space  = $locale["{$letter}_sep_by_space"] ? ' ' : '';

            $value = number_format($value, $right, $locale['mon_decimal_point'],

            $flags['nogroup'] ? '' : $locale['mon_thousands_sep']);

            $value = @explode($locale['mon_decimal_point'], $value);

            $n = strlen($prefix) + strlen($currency) + strlen($value[0]);

            if ($left > 0 && $left > $n) {

                $value[0] = str_repeat($flags['fillchar'], $left - $n) . $value[0];

            }

            $value = implode($locale['mon_decimal_point'], $value);

            if ($locale["{$letter}_cs_precedes"]) {

                $value = $prefix . $currency . $space . $value . $suffix;

            }
            else {

                $value = $prefix . $value . $space . $currency . $suffix;

            }

            if ($width > 0) {

                $value = str_pad($value, $width, $flags['fillchar'], $flags['isleft'] ? STR_PAD_RIGHT : STR_PAD_LEFT);

            }

            $format = str_replace($fmatch[0], $value, $format);
        }

        return $format;
    }

}

//game functions

function shortenStr ($str, $l) {
    
	if (strlen($str) > $l) {
        
			$str = trim(trim(mb_substr($str, 0, $l, 'UTF-8'), '-'), ' ')."&#8230;";
        
	}
    
	return $str;
    
}

function htmllink($row) {

    $expression = array(1 => '@<(link|a|img|script|link)(.*?)<\/(link|a|img|script|link)>@i', 2 => '@<(link|a|img|script|link)(.*?)>@i', 3 => '/<img(.*?)>/i');
    
    foreach($expression as $reg) {
        
        preg_match_all($reg, $row->code, $matches);
    
        foreach($matches['2'] as $subject) {
            preg_match('@(href=|src=|embed).*?["\'](.*?)["\']@i', $subject, $match);

            if(!empty($match['2']) && strpos($match['2'],"http://") != 'FALSE' && strpos($match['2'],"https://") != 'FALSE' && strpos($match['2'],"mailto") != 'FALSE' ) {

                $main_parse = parse_url($row->url);

                $old=$match['2'];

                if(substr($match['2'], 0, 2) == '//') {

                    $match['2'] = substr($match['2'], 0, -2);

                }

                if (substr($match['2'], -1) == '/') {

                    $match['2'] = substr($match['2'], 0, -1);

                }

                $match['2']=fix_url($match['2'], $main_parse);

                $links[]=array('old'=>$old,'url'=>$match['2']);

            }

        }
        
    }
    
    for ($i = 0; $i < count($links); $i++) {
      
        $duplicate = null;
      
        for ($j = $i+1; $j < count($links); $j++) {
            
            if(strcmp($links[$j]['old'],$links[$i]['old']) === 0) {

                  $duplicate = $j;
            
                  break;

            }
            
        }
    
        if (!is_null($duplicate)) {
          
            array_splice($links,$duplicate,1);
            
        }
        
    }
    
	if (count($links) > 0) {
        
		foreach($links as $key => $link) {
            
			$row->code = str_ireplace($link['old'], $link['url'], $row->code);
            
		}
        
	}

	return $row->code;
}

function seo_retry($seo, $number, $table, $type) {
    
	global $setting;
    
	$query = mysqli_query($setting['Lid'],"SELECT * FROM `seonames` WHERE `seo_name` = '".$seo."' AND `type` = '".$type."'");
    
	$count = mysqli_fetch_object($query);
    
	$number = $count->uses;
    
	mysqli_query($setting['Lid'],"SELECT * FROM `".$table."` WHERE `seo_url` = '".$seo."-".$number."'");
    
	$exist = array_pop(mysqli_fetch_array(mysqli_query($Lid,"SELECT FOUND_ROWS()")));
	
    if($exist > 0) {
        
		$number = $number + 1;
        
		mysqli_query($setting['Lid'],"UPDATE `seonames` SET `uses` = `uses` + 1 WHERE `seo_name` = '".$seo."' AND `type` = '".$type."'");
		
        seo_retry($seo,$number,$table);
	
    }	
	else {
        
		return $seo."-".$number;
        
	}
    
}

function create_seoname($name, $id, $type) {
    
	global $setting;
    
	$seo = seoname($name);
    
	if ($id !== 0) {
        
		if ($type == 'game') {
            
			$game = mysqli_fetch_object(mysqli_query($setting['Lid'],"SELECT `name`,`seo_url` FROM `games` WHERE `id` = '".$id."'"));
            
			if ($game->name == $name) {
                
				$seo = $game->seo_url;
                
				return $seo;
                
			}
            
		}
		else if ($type == 'category') {
            
			$cat = mysqli_fetch_object(mysqli_query($setting['Lid'],"SELECT `name`,`seo_url` FROM `cats` WHERE `id` = '".$id."'"));
            
			if ($cat->name == $name) {
                
				$seo = $cat->seo_url;
                
				return $seo;
                
            }
            
		}
        
	}
    
	if ($type == 'game') {
        
		$table="games";
        
	}
	else if ($type == 'category') {
        
		$table="cats";
        
	}
    
	$exist = array_pop(mysqli_fetch_array(mysqli_query($setting['Lid'],"SELECT COUNT(`id`) FROM `".$table."` WHERE `seo_url` = '".$seo."'")));
    
	if($exist > 0) {
        
		$query=mysqli_query($setting['Lid'],"SELECT * FROM `seonames` WHERE `seo_name` = '".$seo."' AND `type` = '".$type."'");
        
		$count = mysqli_fetch_object($query);
        
		$number = $count->uses + 1;
        
		mysqli_query($setting['Lid'],"UPDATE `seonames` SET `uses` = `uses` + 1 WHERE `seo_name` = '".$seo."' AND `type` = '".$type."'");
        
		$seo=seo_retry($seo, $number, $table, $type);
        
	}	
	else {
        
		mysqli_query($Lid,"INSERT INTO `seonames` (`seo_name`, `type`, `uses`) VALUES ('".$seo."', '".$type."', '1')");
        
	}
    
	return $seo;
    
}

function seoname($name) {
    
	$name = stripslashes($name);
    
	$name = strtolower($name);
    
	$name = str_replace("&", "and", $name);
    
	$name = str_replace(" ", "-", $name);
    
	$name = str_replace("---", "-", $name);
    
	$name = str_replace("/", "-", $name);
    
	$name = str_replace("?", "", $name);
    
	$name = preg_replace( "/[\.,\";'\:]/", "", $name );
    
	//$name = urlencode($name);
    
	return $name;
    
}

function feed_category($cat,$feed_setting) {
	if ($cat == 'Action') 
		$cat_id = $feed_setting['category_action'];
	elseif ($cat == 'Adventure' || $cat == 'Adventure & RPG')  
		$cat_id = $feed_setting['category_adventure'];
	elseif ($cat == 'Arcade')  
		$cat_id = $feed_setting['category_arcade'];
	elseif ($cat == 'Autopost')  
		$cat_id = $feed_setting['category_autopost'];
	elseif ($cat == 'Board Game')  
		$cat_id = $feed_setting['category_board_game'];
	elseif ($cat == 'Casino')  
		$cat_id = $feed_setting['category_casino'];
	elseif ($cat == 'Defense')  
		$cat_id = $feed_setting['category_defense'];
	elseif ($cat == 'Dress-Up')  
		$cat_id = $feed_setting['category_dress_up'];
	elseif ($cat == 'Driving' || $cat == 'Racing' || $cat == 'Uphill Racing')  
		$cat_id = $feed_setting['category_driving'];
	elseif ($cat == 'Education')  
		$cat_id = $feed_setting['category_education'];
	elseif ($cat == 'Fighting')  
		$cat_id = $feed_setting['category_fighting'];
	elseif ($cat == 'Multiplayer')  
		$cat_id = $feed_setting['category_multiplayer'];
	elseif ($cat == 'Other')  
		$cat_id = $feed_setting['category_other'];
	elseif ($cat == 'Pimp my' || $cat == 'Customize' || $cat == 'Girls' || $cat == 'Make Over' || $cat == 'Dress Up')  
		$cat_id = $feed_setting['category_customize'];
	elseif ($cat == 'Puzzles')  
		$cat_id = $feed_setting['category_puzzle'];
	elseif ($cat == 'Rhythm' || $cat == 'Music & More')  
		$cat_id = $feed_setting['category_rhythm'];
	elseif ($cat == 'RPG')  
		$cat_id = $feed_setting['category_rpg'];
	elseif ($cat == 'Shooting' || $cat == 'Shooter')  
		$cat_id = $feed_setting['category_shooter'];
	elseif ($cat == 'Sports' || $cat == 'Sports & Racing')  
		$cat_id = $feed_setting['category_sports'];
	elseif ($cat == 'Strategy' || $cat == 'Strategy & Defense' || $cat == 'Tower Defense')  
		$cat_id = $feed_setting['category_strategy'];
	elseif ($cat == 'Jigsaw')  
		$cat_id = $feed_setting['category_jigsaw'];
	elseif ($cat == 'Skill')  
		$cat_id = $feed_setting['category_skill'];
	else 
		$cat_id = $feed_setting['category_other'];
	return $cat_id;
    
}

// search functions

function getKeywordSuggestionsFromGoogle($query) {

    $suggestions = array(); 
	
	$url = 'http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=en-US&q='.urlencode($query);

	$data = url_info($url);

	$data = json_decode($data['response'], true);

    if ($data !== null) {

        $suggestions = (string) $data[1];

    }

    return $suggestions;
}

function map($location, $zoom) {

	global $setting;

	echo '<script src="http://maps.googleapis.com/maps/api/js?key='.$setting["google_map_key"].'"></script>

    	<script type="text/javascript">

	function initialize() {

  		var mapProp = {
    			center: new google.maps.LatLng('.$location.'),
    			zoom:'.$zoom.',
    			mapTypeId: google.maps.MapTypeId.ROADMAP
  		};

		var map = new google.maps.Map(document.getElementById("mapcanvas"),mapProp);
	}

	google.maps.event.addDomListener(window, \'load\', initialize);

    	</script>';


}

function movie($q) {

	$url = 'http://www.omdbapi.com/?t='.urlencode($q).'&y=&plot=short&r=json';

	$data = url_info($url);

	$details = json_decode($data['response']);

	$return = "";

	if($details->Response=='True') {

		if($details->Poster !='N/A')	{

			$return .= '<a href="http://www.imdb.com/title/'.$details->imdbID.'/"><img src="'.$details->Poster.'"></a><br>';

		}

    		$return .= '<a href="http://www.imdb.com/title/'.$details->imdbID.'/">'.$details->Title.' ('.$details->Year.')</a><br>';

		if($details->Rated !='N/A') {

    			$return .= 'Rated : '.$details->Rated.'<br>';

		}

		if($details->Released !='N/A')	{

   			$return .= 'Released Date : '.$details->Released.'<br>';

		}

		if($details->Runtime !='N/A') {

			$return .= 'Runtime : '.$details->Runtime.'<br>';

		}
    	
		if($details->Genre !='N/A')	{

    			$return .= 'Genre : '.$details->Genre.'<br>';

		}

		if($details->Director !='N/A')	{

    			$return .= 'Director : '.$details->Director.'<br>';

		}
	
		if($details->Writer !='N/A') {

    			$return .= 'Writer : '.$details->Writer.'<br>';

		}

		if($details->Actors !='N/A') {

			$return .= 'Actors : '.$details->Actors.'<br>';

		}
    	
		if($details->Plot !='N/A') {

			$return .= 'Plot : '.$details->Plot.'<br>';

		}
    	
		if($details->Language !='N/A')	{

    			$return .= 'Language : '.$details->Language.'<br>';

		}

		if($details->Country !='N/A') {

    			$return .= 'Country : '.$details->Country.'<br>';

		}

		if($details->Awards !='N/A') {

    			$return .= 'Awards : '.$details->Awards.'<br>';

		}

		if($details->Metascore !='N/A') {

    			$return .= 'Metascore : '.$details->Metascore.'<br>';

		}

		if($details->imdbRating !='N/A') {

    			$return .= 'IMDB Rating : '.$details->imdbRating.'<br>';

		}

		if($details->imdbVotes !='N/A') {

    			$return .= 'IMDB Votes : '.$details->imdbVotes.'<br>';

		}

	}

	return $return;

}

function company_info($q) {

	$url = "https://en.wikipedia.org/w/api.php?action=query&titles=".$q."&format=json&exintro=1&rvsection=0&rvparse=1&prop=revisions&rvprop=content&redirects";

	$data = url_info($url);

	$json = json_decode($data['response']);

	$pageid = key($json->query->pages);

	$info = $json->query->pages->$pageid->revisions[0];

	foreach ($info as $key => $main) {

		$html .= $main;

	}

	$return = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $html);

	/*preg_match_all('/(<table class="infobox vcard".*?<\/table>)/si', $html, $infobox);

	preg_match_all('/(<div role="note".*?<\/div>)/si', $html, $notes);

	preg_match_all('/(<p>.*?<\/p>)/si', $html, $other);

	$return = implode(" ", $infobox['1']);*/

	if(!space($return)) {

		$pattern = '/(<tr><th scope="row">Coordinates.*?<\/tr>)/si';

		preg_match_all($pattern, $return, $coordinates);

		if(COUNT($coordinates['1']) > 0) {

			preg_match_all('/<span class="latitude">(.*?)<\/span>/si', $coordinates['1']['0'], $latitude);
			
			preg_match_all('/<span class="longitude">(.*?)<\/span>/si', $coordinates['1']['0'], $longitude);

			$location = $latitude['1']['0'].", ".$longitude['1']['0'];

			$latitude = explode(" ", strtolower(preg_replace("/[^a-zA-Z0-9]+/", " ", $latitude['1']['0'])));

			$longitude = explode(" ", strtolower(preg_replace("/[^a-zA-Z0-9]+/", " ", $longitude['1']['0'])));

			$latitude = DMS2Decimal($latitude['0'], $latitude['1'], $latitude['2'], $latitude['3']);

			$longitude = DMS2Decimal($longitude['0'], $longitude['1'], $longitude['2'], $longitude['3']);

			$location = $latitude.", ".$longitude;

			$zoom = 15;

			$map = map_image($location, $zoom);

			$return = str_ireplace($coordinates['1']['0'], $map, $return);

		}

		$tag_array = array('<td', '<table', '<tr', '<tbody', '<th', '</td', '</table', '</tr', '</tbody', '</th', '<div class="row" colspan="2"', '<div class="mbox-small plainlinks sistersitebox"');

		$replace_array = array('<div class="main"', '<div', '<div class="mainrow" ', '<div', '<div class="row"', '</div', '</div', '</div', '</div', '</div', '<div', '<div');

		$return = str_ireplace($tag_array, $replace_array, $return);

	}


	if(space($return)) {

		$return = implode(" ", $other['1']);
				
	}

	if(space($return)) {

		$return = implode(" ", $notes['1']);

	}

	if(space($return)) {

		$return = movie($q);

	}

	$return = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $return);

	return $return;

}

function weather($geodata) {

	global $setting;

	$num_of_days=5;

	$url='http://api2.worldweatheronline.com/free/v2/weather.ashx?key='.$setting["worldweatheronline"].'&q='.$geodata['town_city'].'&num_of_days='.intval($num_of_days).'&format=json';

	$data = url_info($url);

	$json = json_decode($data['response']);

	echo '<div class="weather_body">
	<div class="weather_main">
	<div class="page_title">Weather at '.$json->data->request[0]->query.'</div>
	<div class="weather_results_main">
	<img class="thumbnail_weather_main" src="'.$json->data->current_condition[0]->weatherIconUrl[0]->value.'"></img><div class="weather_info">Current wind speed is '.$json->data->current_condition[0]->windspeedMiles.' mph blowing to '.$json->data->current_condition[0]->winddir16Point.'</br>It\'s a '.
	$json->data->current_condition[0]->weatherDesc[0]->value.' weather today</div></div><ol class="weather_mains">';
	
	foreach ($json->data->weather as $key => $weather) {
		
		echo '<li class="weather_results">
		<img class="thumbnail_weather" src="'.$weather->hourly[0]->weatherIconUrl[0]->value.'"></img>
		<div class="weather_info_li">
		'.$weather->date.' wind speed was '.$weather->hourly[0]->windspeedMiles.' mph blowing to '.$weather->hourly[0]->winddir16Point.'</br>'.
		$weather->hourly[0]->weatherDesc[0]->value.' weather on '.$weather->date.'
		</div>
		</li>';
		
	}
	
	echo "</ol>
	</div>
	</div>";
	
}

function image($q, $page, $limit) {

	global $setting;

	if($page != 0) {

		$limit = 30;

		$start = round((($page-1)*($limit)));

		$url =  'https://api.datamarket.azure.com/Bing/Search/v1/Image?Query=%27'.urlencode($q).'%27&$skip='.$start.'&$format=json&$top='.$limit.'';

		$data = url_info($url, 2, $setting["bing_key"], $setting["bing_key"]);

		$json = json_decode($data['response']);

		foreach ($json->d->results as $result) {

			$array[]=array('image' => $result->Thumbnail->MediaUrl, 'title' => urldecode($result->Title), 'url' => $result->SourceUrl);

		}


	}


	if($page == 0) {

		$limit = 30;

		$start = round((($page-1)*($limit)));

		$url = "http://www.faroo.com/api?q=".urlencode($q)."&start=".$start."&length=".$limit."&src=images&i=true&key=".$setting["faroo_key"]."&f=json";

		$data = url_info($url);

		$json = json_decode($data['response']);

		foreach ($json->results as $result) {

			if($result->iurl != "") {

				$array[]=array('image' => $result->iurl, 'title' => urldecode($result->title), 'url' => $result->url);

			}

    		}

	}

	if($page == 0) {

		$limit = 6;

		$start = round((($page-1)*($limit)));

		$url = "http://api.pixplorer.co.uk/image?word=".$q."&amount=".$limit."";

		$data = url_info($url);
	
		$json = json_decode($data['response']);

		foreach ($json->images as $result) {

			$array[]=array('url' => $result->imageurl, 'image' => $result->imageurl, 'title' => "");
	
		}

	}

	$array = json_decode(json_encode($array));

	if(count($array) < 1){

		echo '<div class="padding_ten">No results where found for '.htmlspecialchars($q).'</div>';

	}

    	foreach($array as $value) {
       
        	echo '<li class="image_results">
		<a href="'.htmlstring(urldecode($value->url)).'"><div class="thumbnail" style="background-image:url('.htmlstring(urldecode($value->image)).')" ></div></a>
		</li>';

    	}

}

function video($q,$page,$limit) {

	global $setting;
	
	$start=intval(intval($page-1)*$limit);
	
	$url = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q='.urlencode($q).'&maxResults='.$limit.'&key='.$setting["youtube_api"].'&start-index='.intval($start+1);

	$json = json_decode(file_get_contents($url), ture);
	
	foreach($json['items'] as $result) {

		$url_info='https://www.googleapis.com/youtube/v3/videos?part=statistics&id='.$result['id']['videoId'].'&key='.$setting["youtube_api"];

		$json_info = json_decode(file_get_contents($url_info), ture);

		$title = $result['snippet']['title'];

		$type = $result['id']['kind'];

		$description = $result['snippet']['description'];

		$views = $json_info['items'][0]['statistics']['viewCount'];

		if($type == 'youtube#video') {

			$watch = 'https://www.youtube.com/watch?v='.$result['id']['videoId'];

		}
		else if($type == 'youtube#channel'){
			
			$watch = 'https://www.youtube.com/user/'.$result['snippet']['channelTitle'];

		}

		$thumbnail = $result['snippet']['thumbnails']['default']['url'];

		$results[] = array('url' => htmlstring($watch), 'title' => limit_text(htmlstring($title), 5), 'thumbnail' => htmlstring($thumbnail), 'views' => htmlstring($views), 'description'=> limit_text(htmlstring($description), 24));
		
	}

	$data = json_decode(json_encode($results));

	if(count($data) < 1){

   		echo 'No Video results where found.';

	}
	else {

		echo '<ol class="result_mains">';

		foreach($data as $key => $value) {
		
			echo '<li class="video_results">
        		<a class="thumbnail_video_a" href="'.$value->url.'"><img class="thumbnail" src="'.$value->thumbnail.'" /></a>
       		<div class="video_info"><a class="title" href="'.$value->url.'">'.$value->title.'</a><div class="padding_ten">'.$value->description.'</div>
			<div class="video_views">'.number_format($value->views).'+ views</div></div>
      		</li>';

    		}

		echo '</ol>';

	}

}

function news($q, $page, $limit, $bold, $web = 0) {

	global $setting;

	if($page != 0) {

		$limit = 8;

		if($web == 1) {

			$limit = 1;

		}
		else if($page > 1) {

			$old_limit = $limit;

			$limit = 16;

			
		}

		$start = round((($page)*($limit)) - $old_limit);

		$feedurl = "http://www.faroo.com/api?q=".urlencode($q)."&start=".$start."&length=".$limit."&l=en&src=news&key=".$setting["faroo_key"]."&f=rss";
		
		$feed = new DOMDocument();

		$feed->load($feedurl);

		$items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');

		foreach($items as $item) {

  			$title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;

   			$description = $item->getElementsByTagName('description')->item(0)->firstChild->nodeValue;

			$description = str_replace("<br/>", "", $description);

   			$pubDate = $item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue;

   			$guid = $item->getElementsByTagName('guid')->item(0)->firstChild->nodeValue;

			$author = $item->getElementsByTagName('author')->item(0)->firstChild->nodeValue;

   			$pubDate = $item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue;
	
			preg_match('/(<img(?:[^>]*)+>)/i',$description, $match);

			preg_match("@src=[\"']?([^\"]*)[\"']?@i",$match[0], $image);

			$image = $image[1];

			preg_match("@width=[\"']?([^\"]*)[\"']?@i",$match[0], $width);

			$width = $width[1]."px";

			$description = preg_replace('/<a[^>]*>.*?<\/a>/i', '', $description);

			$array[] = array('image_width' => $width, 'image_url' => urlencode(htmlstring($image)), 'source_url' => urlencode(htmlstring($guid)), 'title' => urlencode(htmlstring($title)), 'summary' => urlencode(indextext($description)), 'publish_date' => urlencode(htmlstring($pubDate)), 'source' => urlencode(htmlstring($value->source)), 'author' => urlencode(htmlstring($author))); 
     		
		}

	}


	if($page == 1 && $web == 0) {

		$limit = 8;
		
		$start = round((($page-1)*($limit)));

		$url = "https://webhose.io/search?token=".$setting["webhose_key"]."&format=json&q=".urlencode($q)."%20language%3A(english)%20performance_score%3A%3E0%20(site_type%3Anews%20OR%20site_type%3Ablogs)&size=".$limit."";

		$data = url_info($url);

		$json = json_decode($data['response']);

		foreach ($json->posts as $result) {

			$array[] = array('image_width' => "194px", 'image_url' => urlencode(htmlstring($result->thread->main_image)), 'source_url' => urlencode(htmlstring($result->url)), 'title' => urlencode(htmlstring($result->title)), 'summary' => urlencode($result->text), 'publish_date' => urlencode(htmlstring($result->published)), 'source' => urlencode(htmlstring(indextext($value->source))), 'author' => urlencode(htmlstring($result->thread->site))); 
     		
    		}

	}

	$data = json_decode(json_encode($array));

	if(count($data) == 0 && $web == 0){

   		echo '<span class="error-primary">No results where found for '.htmlspecialchars($q).'</span>';

	}

	foreach($data as $value) { 

		echo '<li class="news_results">
		<div class="news_info">
		<h3><a href="'.htmlstring(urldecode($value->source_url)).'">'.make_bold(htmlstring(limit_text(urldecode($value->title), 10)), $bold).'</a></h3>
		<div class="news_summary">';

		if(!space($value->image_url)) {

			echo '<a href="'.htmlstring(urldecode($value->source_url)).'"><img src="'.htmlstring(urldecode($value->image_url)).'" width="'.$value->image_width.'"></a>';

		}

		echo make_bold(limit_text(urldecode($value->summary), 28), $bold).'</div>';

		if(!space($value->publish_date)) {

			echo ' <b>Publish</b> '.htmlstring(urldecode($value->publish_date));

		}

		if(!space($value->author)) {

			echo ' <b>Auther</b> '.htmlstring(urldecode($value->author));

		}

		if(!space($value->source)) {

			echo ' <b>Source</b> '.htmlstring($value->source);

		}
		
		echo '</div>
		</li>';
		
    	}
	
}

//Crawler

function itemscope(&$array) {
	
	$main_parse = parse_url($array["url"]);

	$html = str_get_html($array['response']);

	$match = $html->find('[itemscope]');

	foreach($match as $key => $itemscope) {

		//$array["itemscope"][$key][$itemscope->itemprop]["outertext"] = mysqli($itemscope->outertext);

		//$array["itemscope"][$key][$itemscope->itemprop]["plaintext"] = mysqli($itemscope->plaintext);

		if(indexpartialtext(indextext($itemscope->itemtype)) !== "") {
		
			if(indexpartialtext(indextext($itemscope->itemprop)) !== "") {

				$array["itemscope"][$key]["itemprop"] = (string) mysqli(indexpartialtext(indextext($itemscope->itemprop)));

			}
			else {

				$parse_url = parse_url($itemscope->itemtype);

				$array["itemscope"][$key]["itemprop"] = (string)  mysqli(strtolower(substr(indexpartialtext(indextext($parse_url["path"])), 1)));

			}

			$array["itemscope"][$key]["itemtype"] = (string) mysqli(indexpartialtext(indextext($itemscope->itemtype)));

		
		}

		if(indexpartialtext(indextext($array[$key]["itemprop"])) == "" && indexpartialtext(indextext($itemscope->itemprop)) !== "") {

			$array["itemscope"][$key]["itemprop"] = (string) mysqli($itemscope->itemprop);

		}

        foreach($itemscope->find('[itemprop]') as $subkey => $itemprop) {

			if(indexpartialtext(indextext($itemprop->itemtype)) !== "") {
		
				if(indexpartialtext(indextext($itemprop->itemprop)) !== "") {

					$array["itemscope"][$key][$itemprop->itemprop]["itemprop"] = (string) mysqli(indexpartialtext(indextext($itemprop->itemprop)));

				}
				else {

					$parse_url = parse_url($itemprop->itemtype);

					$array["itemscope"][$key][$itemprop->itemprop]["itemprop"] = (string) mysqli(strtolower(substr(indexpartialtext(indextext($parse_url["path"])), 1)));

				}

				$array["itemscope"][$key][$itemprop->itemprop]["itemtype"] = (string) mysqli($itemprop->itemtype);

		
			}

			if(indexpartialtext(indextext($array[$key][$itemprop->itemprop]["itemprop"])) == "" && indexpartialtext(indextext($itemprop->itemprop)) !== "") {

				$array["itemscope"][$key][$itemprop->itemprop]["itemprop"] = (string) mysqli($itemprop->itemprop);

			}

			if(indexpartialtext(indextext($itemprop->outertext)) !== "") {

				$array["itemscope"][$key][$itemprop->itemprop]["plaintext"] = (string) mysqli(indexpartialtext(indextext($itemprop->outertext)));

			}

			if(indexpartialtext(indextext($itemprop->itemtype)) !== "") {

				$array["itemscope"][$key][$itemprop->itemprop]["itemtype"] = (string) mysqli($itemprop->itemtype);

			}

			if(indexpartialtext(indextext($itemprop->src)) !== "") {

				$array["itemscope"][$key][$itemprop->itemprop]["scr"] = (string) mysqli(fix_url($itemprop->src, $main_parse));
			
			}
			else if(indexpartialtext(indextext($itemprop->find('[itemprop="image"]')[0])) !== ""){

				$array["itemscope"][$key][$itemprop->itemprop]["scr"] = (string) mysqli(fix_url($itemprop->find('[itemprop="image"]')[0]->scr, $main_parse));

			}
			else if(indexpartialtext(indextext($itemprop->find('[src]')[0])) !== ""){

				$array["itemscope"][$key][$itemprop->itemprop]["scr"] = (string) mysqli(fix_url($itemprop->find('[src]')[0]->scr, $main_parse));

			}


			if(indexpartialtext(indextext($itemprop->href)) !== "") {

				$array["itemscope"][$key][$itemprop->itemprop]["href"] = (string) mysqli(fix_url($itemprop->href, $main_parse));
			
			}
			else if(indexpartialtext(indextext($itemprop->find('[itemprop="url"]')[0])) !== ""){

				$array["itemscope"][$key][$itemprop->itemprop]["href"] = (string) mysqli(fix_url($itemprop->find('[itemprop="url"]')[0]->href, $main_parse));

			}
			else if(indexpartialtext(indextext($itemprop->find('[href]')[0])) !== ""){

				$array["itemscope"][$key][$itemprop->itemprop]["href"] = (string) mysqli(fix_url($itemprop->find('[href]')[0]->href, $main_parse));

			}
			
			if($itemprop->tag == "meta") {

				$array["itemscope"][$key][$itemprop->itemprop]["content"] = (string) mysqli(indexpartialtext(indextext($itemprop->content)));

			}

			if($itemprop->outertext !== "" && $array["itemscope"][$key][$itemprop->itemprop]["plaintext"] !== $itemprop->outertext) {

				$expressions = array('/&[a-z]{1,6};/',
				'/&nbsp;/',
				'@\s\s+@',
				'@\s+@',
				'/(<|>)\1{2}/si');

				$string = indexpartialtext(preg_replace($expressions, ' ', $itemprop->outertext));  

				$string = preg_replace($expressions, ' ', $string);  

				$string = trim(replaceWhitespace($string));

				$array["itemscope"][$key][$itemprop->itemprop]["outertext"] = (string) mysqli($string);

			}

    		}

		$match[$key]->outertext = "";

	}

	return $array;

}

function longest_common_substring($words) {

  	$words = array_map('strtolower', array_map('trim', $words));
  
	$sort_by_strlen = create_function('$a, $b', 'if (strlen($a) == strlen($b)) { return strcmp($a, $b); } return (strlen($a) < strlen($b)) ? -1 : 1;');
  
	usort($words, $sort_by_strlen);

  	$longest_common_substring = array();

  	$shortest_string = str_split(array_shift($words));

  	while (sizeof($shortest_string)) {

    		array_unshift($longest_common_substring, '');

    		foreach ($shortest_string as $ci => $char) {
      
			foreach ($words as $wi => $word) {
        
				if (!strstr($word, $longest_common_substring[0].$char)) {

          				break 2;
        
				}
      
			}

      		$longest_common_substring[0].= $char;

    		} 

		array_shift($shortest_string);

  	}

  	usort($longest_common_substring, $sort_by_strlen);

  	return array_pop($longest_common_substring);

}

function insert_keywords($string, &$meta) {

	global $setting;

	$array = array('1' => '/\p{Han}+/u', '2' => '/[^[:alpha:]]/', '3' => '/[^a-zA-Z]+/');

	$string = preg_replace($array, ' ', $string);

	$words = array_count_values(str_word_count(strtolower($string), 1));

	foreach($words as $key => $match) {

		$count = array_pop(mysqli_fetch_row(mysqli_query($setting['crawler_Lid'],"SELECT COUNT(`id`) FROM `keywords` WHERE `word`='".$key."'")));
		
		if($count == 0) {

			mysqli_query($setting['crawler_Lid'],"INSERT INTO `keywords` (`word`) VALUES ('".strtolower($key)."')");

		}

	}

}

function insert_fulltext($string, &$meta) {

	global $setting;

	$array = array('1' => '/\p{Han}+/u', '2' => '/[^[:alpha:]]/', '3' => '/[^a-zA-Z]+/');

	$string = preg_replace($array, ' ', $string);

	$words = array_count_values(str_word_count(strtolower($string), 1));

	$total_records = array_pop(mysqli_fetch_row(mysqli_query($setting['crawler_Lid'],"SELECT COUNT(`id`) FROM `meta`")));

	foreach($words as $key => $match) {

		$query = mysqli_query($setting['crawler_Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `count` FROM `keywords` WHERE `word`='".$key."'");

		$row = mysqli_fetch_object($query);

		$total_count = array_pop(mysqli_fetch_row(mysqli_query($setting['crawler_Lid'],"SELECT FOUND_ROWS()")));
		
		if($total_count > 0) {

			$total_matches = array_pop(mysqli_fetch_row(mysqli_query($setting['crawler_Lid'],"SELECT COUNT(`id`) FROM `full_text` WHERE `keyword`='".$row->id."'")));

			$inverse_frequency = log10($total_records/$total_matches);

			$row->count = $match + $row->count;

			$weight = $inverse_frequency * $row->count;

			mysqli_query($setting['crawler_Lid'], "UPDATE `keywords` SET `count`='".$row->count."',`weight`='".$weight."',`inverse_frequency`='".$inverse_frequency."' WHERE `id` = '".$row->id."'");
			
		}
		else {

			$inverse_frequency = log10($total_records);

			$weight = $inverse_frequency * $match;

			mysqli_query($setting['crawler_Lid'],"INSERT INTO `keywords` (`word`, `count`, `weight`, `inverse_frequency`) VALUES ('".strtolower($key)."', '".$match."', '".$weight."', '".$inverse_frequency."')");

			$row->id=mysqli_insert_id($setting['crawler_Lid']);

		}

		mysqli_query($setting['crawler_Lid'], "INSERT INTO `full_text` (`meta`, `count`, `keyword`) VALUES ('".$meta."', '".$match."', '".$row->id."');");

	}

}

function insert_link($href, &$row) {

	global $setting;

	$parse = parse_url($href);

	$parse = main_domain($parse['scheme']."://".$parse['host']);

	$main = mysqli($parse['main'].$parse['extension']);

	if(substr($main, 0, 1) == ".") {

		$main = substr($main, 1);

	}

	if(substr($main, -1) == "/") {

		$main = substr($main, 0 , -1);

	}

	$domain = mysqli_fetch_object(mysqli_query($setting['crawler_Lid'],"SELECT SQL_CALC_FOUND_ROWS `id`, `company` FROM `domain` WHERE `domain`='".$main."'"));

	$count = array_pop(mysqli_fetch_row(mysqli_query($setting['crawler_Lid'],"SELECT FOUND_ROWS()")));

	$company->id = $domain->company;

	if($count == 0) {

		mysqli_query($setting['crawler_Lid'],"INSERT INTO `domain` (`domain`, `company`, `alexa`) VALUES ('".$main."', '".$company->id."', '".$alexa."')");

		$domain->id = mysqli_insert_id($setting['crawler_Lid']);

	}

	$dept = 1;

	if($domain->id == $row->domain) {

		$dept = $row->dept + 1;

	}
	
	mysqli_query($setting['crawler_Lid'],"INSERT INTO `url`(`url`, `domain`, `company`, `dept`) VALUES ('".$href."', '".$domain->id."', '".$company->id."', '".$dept."')");

	$array['id'] = mysqli_insert_id($setting['crawler_Lid']);

	$array['domain'] = $domain->id;

	return $array;

}

function urlLooper(&$array, &$row) {
    
	global $setting, $html_to_array;

	$parse = parse_url($array['url']);

	foreach($array["a"] as $match) {

		$href = $match["href"];

		if ($href != null) {
			
			$href = mysqli(fix_url($href, $parse));

			$url_id = mysqli_fetch_object(mysqli_query($setting['crawler_Lid'],"SELECT `id`, `domain`, COUNT(`id`) as `count` FROM `url` WHERE `url`='".$href."'"));

			if($url_id->count == 0) {

				$insert_info = insert_link($href, $row);

			}

		}

	}

}

function urladder(&$row) {

	global $setting;

	$array = url_info(strtolower(redirection($row->url)), 1);

	if($array['status'] == 200) {

		if(robots_allowed($array['url'])) {
				
			$array = site_info($array);	

			$array = itemscope($array);		

			$count = array_pop(mysqli_fetch_row(mysqli_query($setting['crawler_Lid'],"SELECT COUNT(`id`) FROM `meta` WHERE `new_url`='".$array['url']."'")));

			if ($count == 0) {

				$new_array = $array;

				$meta = $array['content'];

				$density = $array['density'];

				$title = $array['title'];

				$description = $array['description'];

				$new_url = $array['url'];

				$response = mysqli($array['response']);

				//$full_text = mysqli($array['full_text']);

				unset($new_array['response']);

				unset($new_array['content']);

				unset($new_array['density']);

				unset($new_array['description']);

				unset($new_array['url']);

				mysqli_query($setting['crawler_Lid'], "INSERT INTO `meta` (`word`, `array`, `url`, `domain`, `company`, `density`, `title`, `description`, `keywords`, `new_url`) VALUE ('".$meta."', '".mysqli(serialize($new_array))."', '".$row->id."', '".$row->domain."', '".$row->company."', '".$density."', '".$title."', '".$description."', '".$keywords."', '".$new_url."')");

				$metaid->id = mysqli_insert_id($setting['crawler_Lid']);

				if($setting['getkeywords'] == 1) {

					$words_check = $meta.".".$description.".".$title.".";

					insert_keywords($words_check, $metaid->id);

				}

				mysqli_query($setting['crawler_Lid'], "INSERT INTO `html` (`meta`, `html`) VALUE ('".$metaid->id."', '".$response."')");

			}

		}

	}

	mysqli_query($setting['crawler_Lid'], "UPDATE `url` SET `indexed` = Now() WHERE `url`.`id` = '".$row->id."';");

	return $array;

}

function pagerank($damping_factor, $unique_domain, $function = 0, $domain = 0) {

	global $setting;

	if($function == 0 || $function == 1) {

		if($function == 0) {

			$sql = "SELECT COUNT(*) AS `count`, `found_domain`, `domain` FROM `map` WHERE (`domain` = '".implode("' OR `domain` = '", $unique_domain)."') AND (`found_domain` = '".implode("' OR `found_domain` = '", $unique_domain)."') GROUP BY `found_domain`, `domain`";

		}
		else if($function == 1) {

			$sql = "SELECT COUNT(*) AS `count`, `found_domain`, `domain` FROM `map` GROUP BY `found_domain`, `domain`";

		}

		$query = mysqli_query($setting['crawler_Lid'], $sql);

		while ($row = mysqli_fetch_object($query)) {

			if($row->found_domain != $row->domain) {

				$array[$row->domain][$row->found_domain] = $row->count;

			}

		}

	}
	else if($function = 2) {

		$sql = "SELECT `page_rank` FROM `domain` WHERE `id` = '".$domain."'";

		$row = mysqli_fetch_object(mysqli_query($setting['crawler_Lid'], $sql));

		$array = $row->page_rank;

		if($array == 0) {

			$array = 1;

		}

	}

	return $array;

}

function query_building($q, $common, $type = 0) {

	global $setting;

	foreach($common as $key => $main) {

		$array[$main] = $main;

		$stem = PorterStemmer::Stem($main);

		foreach($setting['english_suffix'] as $keys => $match) {

			$array[$stem.$match] = '('.$stem.$match.')';

		}

	}

	$matchs = implode(" ", $array);

	$sql = "SELECT

	`entries`.`word`

	FROM

	`entries`

	WHERE `entries`.`word`='".$matchs."'

	LIMIT 1";

	$query = mysqli_query($setting['crawler_Lid'], $sql);

	while ($row = mysqli_fetch_object($query)) {

		if(array_key_exists($row->word, $array) && $type == 0) {

			$words[$row->word] = $row->word;

		}
		else if($type == 1) {

			$words[$row->word] = $row->word;

		}

	}

	return $words;

}

function get_result($key) {

	global $setting;

    $sql = "SELECT

	`meta`.`array`,
	`meta`.`domain`,
	`meta`.`id`,
	`meta`.`title`,
	`meta`.`new_url`,
	`meta`.`description`,
	`meta`.`word`,
	SUBSTRING(`meta`.`word`, LOCATE('".$rank."', `meta`.`word`) -200, CHAR_LENGTH(' ') + 200) as `match`

	FROM

	`meta`

	WHERE `meta`.`id` = '".$key."'";

	$row = mysqli_fetch_object(mysqli_query($setting['Lid'], $sql));

	$new_array = unserialize(stripslashes(base64_decode($row->array)));

	if(space($row->match)) {

		$row->match = implode(" ", $new_array['p']);

	}
	else {

		$row->match = substr(strstr(rtrim($row->match,strrchr($row->match," "))," "),1);

	}

	$summaryTool = new \DivineOmega\PHPSummary\SummaryTool($row->title, $row->match);

	$row->match = $summaryTool->getSummary();

	if(space($row->description)) {

		$row->description = $row->match;

	}

	if(space($row->title)) {

		$row->title = $row->match;

	}

	$title = urlencode(limit_text($row->title, 8));

	$description = urlencode(limit_text($row->description, 25));

	$array['title'] = $title;

	$array['abstract'] = $description;

	$array['url'] = $row->new_url;

	$array['old_url'] = $row->url;

	$array['relevance'] = $row->relevance;

	$array['id'] = $row->id;

	return $array;

}

function web($expand, $common, $q, $page, $limit, $deep_search, $query_row) {

	global $setting;

	foreach($expand as $key => $matchs) {

		$match[] = '+'.$key;

	}

	$matchs = implode(' ', array_unique($match));

	$start = ($page-1) * $limit;

    $map = array_filter(sql_relevance($q, $deep_search, $expand, $matchs));

	$count = COUNT($map);

	$array['count'] = $count;

	$array['stem'] = $query_string;

	$map_key = array_keys($map);

	if($count > 0) {

		for ($i = max(0, $start); $i <= min($start + ($limit - 1), $count); $i++) {

			$array[$map_key[$i]] = json_decode(json_encode(get_result($map_key[$i])));

		}

	}

	return $array;

}

function sql_relevance($q, $deep_search, $expand, $matchs) {

	global $setting;

	$lib_search = new Libs_Search($q);

	$stemmedmatchs = $lib_search->GetSearchQueryString()." ".$matchs;

	$expand_count = COUNT($expand);

	$temp = "CREATE TABLE `temp_meta` (
		`id` INT,
		`domain` varchar(40) not null,
		`relevance` double not null
	);";

	mysqli_query($setting['Lid'], $temp);

	$relevance = "INSERT INTO `temp_meta` (`id`, `domain`, `relevance`) SELECT `meta`.`id`, `meta`.`domain`, ";

	$relevance .= "((20 * (MATCH(`meta`.`word`) AGAINST ('".$stemmedmatchs."'))) + (MATCH(`meta`.`description`) AGAINST ('".$stemmedmatchs."')) + (15 * (MATCH(`meta`.`title`) AGAINST ('".$stemmedmatchs."')))";

	$relevance .= " + (10 * (MATCH(`meta`.`domain`) AGAINST ('".$stemmedmatchs."')))";

	if($deep_search == 1) {

		$relevance .= " + (20 * (MATCH(`meta`.`word`) AGAINST ('".$matchs."' IN BOOLEAN MODE))) + (MATCH(`meta`.`description`) AGAINST ('".$matchs."' IN BOOLEAN MODE)) + (15 * (MATCH(`meta`.`title`) AGAINST ('".$matchs."' IN BOOLEAN MODE)))";

		$relevance .= " + (20 * (MATCH(`meta`.`word`) AGAINST ('".$matchs."' IN NATURAL LANGUAGE MODE))) + (MATCH(`meta`.`description`) AGAINST ('".$matchs."' IN NATURAL LANGUAGE MODE)) + (15 * (MATCH(`meta`.`title`) AGAINST ('".$matchs."' IN NATURAL LANGUAGE MODE)))";

		if($expand_count > 0) {

			$relevance .= " + (10 * (MATCH(`meta`.`domain`) AGAINST ('".$matchs."' IN BOOLEAN MODE)))";

			$relevance .= " + (10 * (MATCH(`meta`.`domain`) AGAINST ('".$matchs."' IN NATURAL LANGUAGE MODE)))";

		}

	}

	$relevance .= ") AS `relevance`

	FROM `meta`

	WHERE `meta`.`title` != '' AND `meta`.`word` != ''

	HAVING Relevance > 0;";

	mysqli_query($setting['Lid'], $relevance);

	$sql = "SELECT `temp_meta`.`id`, `temp_meta`.`domain`, `temp_meta`.`relevance` FROM `temp_meta";
    
    //$sql = "SELECT `temp_meta`.`id`, `temp_meta`.`domain`, `temp_meta`.`relevance` FROM `temp_meta` WHERE `temp_meta`.`relevance` > (SELECT AVG(`temp_meta`.`relevance`) FROM `temp_meta` WHERE `temp_meta`.`relevance` > (SELECT AVG(`temp_meta`.`relevance`) FROM `temp_meta`));";

	$query = mysqli_query($setting['Lid'], $sql);

	$drop = "DROP TABLE `temp_meta`";

	mysqli_query($setting['Lid'], $drop);

	while ($row = mysqli_fetch_object($query)) {

		$map[$row->id] = round($row->relevance);

	}

	arsort($map);

	return $map;

}

function sql_query_data($id) {

	global $setting;

	$sql = "SELECT `meta`, `order` FROM `result` WHERE `query` = ".$id."";

	$query = mysqli_query($setting['Lid'], $sql);

	while ($row = mysqli_fetch_object($query)) {

		$map[$row->meta] = $row->order;

	}

	arsort($map);

	return $map;

}

function isValidEmail($email) {
    
    $regex = '/([a-z0-9_]+|[a-z0-9_]+\.[a-z0-9_]+)@(([a-z0-9]|[a-z0-9]+\.[a-z0-9]+)+\.([a-z]{2,4}))/i';

    return preg_match($regex, $email);
    
}

function is_valid_domain_name($site) {
    
	return !preg_match('/^[a-z0-9\-]+\.[a-z]{2,100}(\.[a-z]{2,14})?$/i', $site);
    
}

function bytesToSize($bytes, $precision = 2) {  
    
    $kilobyte = 1024;

    $megabyte = $kilobyte * 1024;

    $gigabyte = $megabyte * 1024;

    $terabyte = $gigabyte * 1024;

    if (($bytes >= 0) && ($bytes < $kilobyte)) {

        return $bytes . ' B';

    } 
    elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {

        return round($bytes / $kilobyte, $precision) . ' KB';

    } 
    elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {

        return round($bytes / $megabyte, $precision) . ' MB';

    } 
    elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {

        return round($bytes / $gigabyte, $precision) . ' GB';

    } 
    elseif ($bytes >= $terabyte) {

        return round($bytes / $terabyte, $precision) . ' TB';

    } 
    else {

        return $bytes . ' B';

    }
    
}

function ratio_check_ratio($url) {
    
	$real_content = ratio_file_get_contents_curl($url);
    
	$page_size = mb_strlen($real_content, '8bit');
    
	$content = ratio_strip_html_tags($real_content);
    
	$text_size = mb_strlen($content, '8bit');
    
	$content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", " ", $content);

	$len_real = strlen($real_content);
    
	$len_strip = strlen($content);
    
	return round((($len_strip/$len_real)*100), 2);
    
}

function check_compressed($url) {
    
	$ch = curl_init($url);
    
    curl_setopt($ch,CURLOPT_HTTPHEADER,array('Accept-Encoding: gzip, deflate'));
    
    curl_setopt($ch, CURLOPT_HEADER, 1);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $buffer = curl_exec($ch);
    
    $curl_info = curl_getinfo($ch);
    
    curl_close($ch);
    
    $header_size = $curl_info["header_size"];
    
    $headers = substr($buffer, 0, $header_size);
    
    $body = substr($buffer, $header_size);

    $encoding=getEncoding($headers);

    if ($encoding) {
        
        return "Using: ".$encoding;
        
    }
    else{
        
        return "None";
        
    }

}

function get_headings_tag($html) {
	
    $headings = array(
        'h1' => array(),
        'h2' => array(),
        'h3' => array(),
        'h4' => array(),
        'h5' => array(),
        'h6' => array(),
    );
    
    $pattern = "<(h[1-6]{1})(.+)?>(.*)</h[1-6]{1}(?:[^>]*)>";
    
    preg_match_all("#{$pattern}#iUs",$html, $matches);
    
    $sizes = isset($matches[1]) ? $matches[1] : array();
    
    foreach($sizes as $id => $size) {
        
        $headings[strtolower($size)][] = strip_tags(trim($matches[3][$id]));
        
    }
    
    return $headings;
    
}

function is_Iframe($html) {
    
    $pattern = "#<iframe[^>]+>.*?</iframe>#is";
    
    return preg_match("$pattern", $html);
    
}

function is_Flash($html) {
    
    $pattern = "#<object[^>]*>(.*?)</object>#is";
    
    return preg_match("$pattern", $html);

}

function isInlineCss($html) {
    
    $pattern = "#<(.+)style=\"[^\"].+\"[^>]*>(.*?)<\/[^>]*>#is";
    
    return preg_match("$pattern", $html);
    
}

function get_sv_Signature($url,$format=0) {
    
	$url=parse_url($url);
    
	$end = "\r\n\r\n";
    
	$fp = fsockopen($url['host'], (empty($url['port'])?80:$url['port']), $errno, $errstr, 30);
    
	if ($fp) {
        
		$out  = "GET / HTTP/1.1\r\n";
        
		$out .= "Host: ".$url['host']."\r\n";
        
		$out .= "Connection: Close\r\n\r\n";
        
		$var  = '';
        
		fwrite($fp, $out);
        
		while (!feof($fp)) {
            
			$var.=fgets($fp, 1280);
            
			if(strpos($var,$end)) {
                
				break;
                
            }
            
		}
        
		fclose($fp);

		$var=preg_replace("/\r\n\r\n.*\$/",'',$var);
        
		$var=explode("\r\n",$var);
        
		if($format) {
            
			foreach($var as $i) {
                
				if(preg_match('/^([a-zA-Z -]+): +(.*)$/',$i,$parts)) {
                    
					$v[$parts[1]]=$parts[2];
                    
                }
                
			}
            
			return $v;
            
		}
		else {
            
			return $var;
            
        }
        
	}
    
}

function alexaRank($site) {
    
	$alexa=file_get_contents("http://webtoolsoft.com/api/get_data.php?alexa=true&url=".$site);

	$detail_alexa = json_decode($alexa);
    
	$alexa_rank = $detail_alexa ->alexa_rank;
    
	$alexa_rank = ($alexa_rank == '' ? "No Rank" : $alexa_rank);
    
	$alexa_pop = $detail_alexa ->alexa_pop;
    
	$alexa_pop = ($alexa_pop == '' ? "No Rank" : $alexa_pop);
    
	$regional_rank = $detail_alexa ->regional_rank;
    
	$regional_rank = ($regional_rank == '' ? "No Rank" : $regional_rank);
	
	return array ($alexa_rank,$alexa_pop,$regional_rank);
    
}

function get_top_country_alexa($url) {
    
    $doc = new DomDocument;

    @$doc->loadHTMLFile('http://www.alexa.com/siteinfo/'.$url);

    $data = @$doc->getElementById('demographics_div_country_table');

    $my_data = $data->getElementsByTagName('tr');
    
    $check_data = null;
    
    $countries = array();
    
    foreach ($my_data as $node) {
        
        foreach($node->getElementsByTagName('a') as $href) {
            
            preg_match('/([0-9\.\%]+)/',$node->nodeValue, $match);

            if($href->nodeValue == ' sign up and get certified'){
                
                $check_data = 'null';
                
            }
            else{
            
                $countries[] = array(
                'country' => $href->nodeValue,
                'percent' => $match[0],
                );
                
            }

        }

    }      

    if($check_data == 'null') {
        
        return $check_data;
        
    }
    else{
        
        return $countries;
        
    }

}

function dnsblookup($ip) {
    
	$listed = null;
    
	$dnsbl_lookup = array(
		"dnsbl-1.uceprotect.net",
		"dnsbl-2.uceprotect.net",
		"dnsbl-3.uceprotect.net",
		"dnsbl.dronebl.org",
		"dnsbl.sorbs.net",
		"bl.spamcop.net",
		"block.dnsbl.sorbs.net",
		"zen.spamhaus.org");
    
	if ($ip) {
        
		$reverse_ip = implode(".", array_reverse(explode(".", $ip)));
        
		foreach ($dnsbl_lookup as $host) {
            
			if (checkdnsrr($reverse_ip . "." . $host . ".", "A")) {
                
				$listed .= $host . ', ';
                
			}
            
		}
        
	}
    
	if ($listed) {
        
		return 'Your Server IP is Blacklist: '.substr($listed,0,strlen($listed)-2);
        
	} 
    else {
        
		return 'Your Server IP is not blacklisted';
        
	}
    
}

function checkSafeBrowsing($longUrl) {
    
	$safebrowsing;
    
	$safebrowsing['api_key'] = "ABQIAAAAOQY5PG65Sz64pzYOK6KlmhQjd04VwKOOk1G-Nk48V5R2oPhf3g";
    
	$safebrowsing['api_url'] = "https://sb-ssl.google.com/safebrowsing/api/lookup";
		
	$url = $safebrowsing['api_url']."?client=checkURLapp&";
    
	$url .= "apikey=".$safebrowsing['api_key']."&appver=1.0&";
    
	$url .= "pver=3.0&url=".urlencode($longUrl);
 
	$ch = curl_init();
    
	$timeout = 5;
    
	curl_setopt($ch,CURLOPT_URL,$url);
    
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    
	$data = curl_exec($ch);
    
	$httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
	curl_close($ch);
    
	return $httpStatus;
    
}

function get_PageSize($url){ 
    
   $return = strlen(file_get_contents($url));
    
   return $return; 
    
}

function getDoctype($html) {
    
    $doctypes = array(
    'HTML 5' => '<!DOCTYPE html>',
    'HTML 4.01 Strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
    'HTML 4.01 Transitional' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
    'HTML 4.01 Frameset' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
    'XHTML 1.0 Strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
    'XHTML 1.0 Transitional' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
    'XHTML 1.0 Frameset' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
    'XHTML 1.1' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
    );
    
    preg_match("#<!DOCTYPE[^>]*>#is", $html, $matches);
    
    if(!isset($matches[0])) {
        
	   return false;
    
    }
    
    return array_search(strtolower(preg_replace('/\s+/', ' ', trim($matches[0]))), array_map('strtolower', $doctypes));
    
}

function getDeprecatedTags($html) {
    
	$deprecated = array();
    
	$deprectaedTags = array(
	'acronym', 'applet', 'basefont','listing', 'plaintext','big', 'center', 'dir', 'font', 'frame', 'frameset',
	'isindex', 'noframes','xmp', 's', 'strike', 'tt', 'u',
	);
    
	$pattern = "<(".implode("|", $deprectaedTags).")( [^>]*)?>";
    
	preg_match_all("#{$pattern}#is", $html, $matches);
    
	foreach($matches[1] as $tag) {
        
		if(isset($deprecated[$tag])) {
            
			$deprecated[$tag]++;
            
        }
		else {
            
			$deprecated[$tag] = 1;
            
        }
        
	}
    
	return $deprecated;
    
}

function issetNestedTables($html) {
    
	$pattern = "<(td|th)(?:[^>]*)>(.*?)<table(?:[^>]*)>(.*?)</table(?:[^>]*)>(.*?)</(td|th)(?:[^>]*)>";
    
	return preg_match("#{$pattern}#is", $html);
    
}

function isEmail($html) {
	$pattern="(?:[a-z0-9!#$%&'*+=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])";
	return preg_match("/{$pattern}/is", $html);
}

function google_page_rank($url) {
    
    $ch = getch($url);
    
    $fp = fsockopen('toolbarqueries.google.com', 80, $errno, $errstr, 30);
    
    if ($fp) {
        
        $out = "GET /tbr?client=navclient-auto&ch=$ch&features=Rank&q=info:$url HTTP/1.1\r\n";
        
        $out .= "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:28.0) Gecko/20100101 Firefox/28.0\r\n";
        
        $out .= "Host: toolbarqueries.google.com\r\n";
        
        $out .= "Connection: Close\r\n\r\n";
        
        fwrite($fp, $out);
        
        while (!feof($fp)) {
            
            $data = fgets($fp, 128);
            
            $pos = strpos($data, "Rank_");
            
            if ($pos === true) {
                
                $pager = substr($data, $pos + 9);
                
                $pager = trim($pager);
                
                $pager = str_replace("\n", '', $pager);
                
                return $pager;
            }
            
        }
        
        fclose($fp);
        
    }
    
}

function get_backlink($domain) {
    
    $google = file_get_contents('http://www.google.com/search?q='.$domain);

    $scrapedItem = preg_match_all('/About.*?results/i', $google, $matches, PREG_PATTERN_ORDER);

    $results = $matches[0][0];

    $scrapedItem2 = preg_match_all('/[1-9](?:\d{0,2})(?:,\d{3})*(?:\.\d*[1-9])?|0?\.\d*[1-9]|0/i', $results, $matches2, PREG_PATTERN_ORDER);
    
    return $matches2[0][0];

}

function get_keywords($main_term) {
    
    $keyword_url = 'http://suggestqueries.google.com/complete/search?output=firefox&client=firefox&hl=en-US&q='.urlencode($main_term);

    $data = file_get_contents($keyword_url);

    if((preg_match( "/\/[a-z]*>/i", $data ) == 0) && (strpos($data,"[") !== false)){

        $lines = explode (",",$data);

        if (!empty($lines)) {

            foreach ($lines as $line) {

                $exclude = array('"', "[", "]");

                $line = str_replace($exclude, "", $line);

                $line = trim($line);

                if(!empty($line)){

                    $keyword[] = $line;

                } 

            }	

        }	
        
    }
    
    return $keyword;

}

function social_stats($domain) {
    
    $facebook_url="https://graph.facebook.com/?id=http://".$domain;

    $facebook = file_get_contents($facebook_url);

    $json = json_decode($facebook, TRUE);

    $comment = $json['share']['comment_count'];

    $share = $json['share']['share_count'];

    $social['facebook']['comment'] = (string) $comment;

    $social['facebook']['share'] = (string) $share;
    
    
    $buffer_url="https://api.bufferapp.com/1/links/shares.json?url=http://".$domain;

    $buffer = file_get_contents($buffer_url);

    $json = json_decode($buffer, TRUE);

    $buffer_share = $json['shares'];

    $social['buffer'] = (string) $buffer_share;

    
    $pinterest_url = "https://api.pinterest.com/v1/urls/count.json?callback=jsonp&url=http://".$domain;

    $pinterest = file_get_contents($pinterest_url);

    $result = explode(",",$pinterest);

    $result = implode($result);

    $pins = strstr($result, "count"); 

    $pins = strstr($pins, "}",true); 

    $pins=str_replace('count":', '', $pins);	

    $social['pinterest'] = (string) $pins;


    $linkedin_url= "https://www.linkedin.com/countserv/count/share?url=http://".$domain."&format=json";

    $linkedin = file_get_contents($linkedin_url);

    $json = json_decode($linkedin, TRUE);

    $linkedin = $json['count'];

    $social['linkedin'] = (string) $linkedin;


    $stumbleupon_url="https://www.stumbleupon.com/services/1.01/badge.getinfo?url=http://".$domain;

    $stumbleupon = file_get_contents($stumbleupon_url);

    $json = json_decode($stumbleupon, TRUE);

    $stumbleupon = $json['result']['views'];

    $social['stumbleupon'] = (string) $stumbleupon;
    
    
    $obj=new shareCount("http://".$domain);  //Use your website or URL

    $google=$obj->get_plusones(); //to get google plusones

    $social['google'] = (string) $google;
    
    return $social;
    
}

function alexa_rank($domain) {
    
    $xml = simplexml_load_file('http://data.alexa.com/data?cli=10&dat=snbamz&url='.$domain);

    $rank = isset($xml->SD[1]->POPULARITY)?$xml->SD[1]->POPULARITY->attributes()->TEXT:0;
    
    return $rank;
    
}

function getSEM_rank($url){
    
    $query = 'http://us.backend.semrush.com/?action=report&type=domain_rank&domain='.$url;

    $data=get_Page_Data($query);

    $data=json_decode($data,true);

    $da = isset($data['rank']['data'][0])?$data['rank']['data']:array();

    if(!isset($data['rank']['data'][0])) {

        $da = 'No data';

    }

    return $da;
    
}

function getDomainName($url) {
    
	$url = Trim($url);
    
	$url = preg_replace("/^(http:\/\/)*/is", "", $url);
    
	$url = preg_replace("/^(https:\/\/)*/is", "", $url);
    
	$url = preg_replace("/\/.*$/is" , "" ,$url);
    
	return $url;
    
}

function get_root_domain($url){
    
    $pattern = '/\w+\..{2,3}(?:\..{2,3})?(?:$|(?=\/))/i';
    
    if (preg_match($pattern, $url, $matches) === 1) {
        
        return $matches[0];
        
    }
    
}

function rawheader($url) {
    
    $curl = curl_init();
    
    curl_setopt($curl, CURLOPT_URL, $url);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    
    curl_setopt($curl, CURLOPT_HEADER, 1);
    
    curl_setopt($curl, CURLOPT_NOBODY, 1);
    
    $output = curl_exec($curl);

    curl_close($curl);

    $headers=array();

    $data=explode("\n",$output);

    $headers['status'] = $data[0];

    array_shift($data);

    foreach($data as $part){
        
        $middle=explode(":",$part);
        
        $headers[trim($middle[0])] = (string) trim($middle[1]);
        
    }
    
    return $headers;
    
}

function curlheader($array) {

    $data=explode("\n",$array['response_header']);

    $array['header']['status'] = $data[0];

    array_shift($data);

    foreach($data as $part){
        
        $middle=explode(":",$part);
        
        $array['header'][trim($middle[0])] = (string) trim($middle[1]);
        
    }
    
    return $array;
    
}

function domain_rank($array) {
    
    if($array['social']['facebook']['share'] >= 500001){ 

        $array['rate'] = $array['rate']+15; 

    }
    else if(($array['social']['facebook']['share'] <= 500000) && ($array['social']['facebook']['share'] >= 100001)){ 

        $$array['rate'] = $array['rate']+12; 

    }	
    else if(($array['social']['facebook']['share'] <= 100000) && ($array['social']['facebook']['share'] >= 10001)){ 

        $array['rate'] = $array['rate']+9; 

    }	
    else if(($array['social']['facebook']['share'] <= 10000) && ($array['social']['facebook']['share'] >= 1001)){ 

        $array['rate'] = $array['rate']+7; 

    }	
    else if(($array['social']['facebook']['share'] <= 1000) && ($array['social']['facebook']['share'] >= 101)){ 

        $array['rate'] = $array['rate']+5; 

    }
    else if(($array['social']['facebook']['share'] <= 100) && ($array['social']['facebook']['share'] >= 10)){ 

        $array['rate'] = $array['rate']+3; 

    }	

    if($array['google']['backlink'] >= 100000001){ 

        $array['rate'] = $array['rate']+15; 

    }
    else if(($array['google']['backlink'] <= 100000000) && ($array['google']['backlink'] >= 10000001)){ 

        $array['rate'] = $array['rate']+12; 

    }	
    else if(($array['google']['backlink'] <= 10000000) && ($array['google']['backlink'] >= 1000001)){ 

        $array['rate'] = $array['rate']+10; 

    }	
    else if(($array['google']['backlink'] <= 100000) && ($array['google']['backlink'] >= 10001)){ 

        $array['rate'] = $array['rate']+7; 

    }
    else if(($array['google']['backlink'] <= 10000) && ($array['google']['backlink'] >= 1001)){ 

        $array['rate'] = $array['rate']+5; 

    }	
    else if(($array['google']['backlink'] <= 1000) && ($array['google']['backlink'] >= 101)){ 

        $array['rate'] = $array['rate']+3; 

    }
    else if(($array['google']['backlink'] <= 100) && ($array['google']['backlink'] >= 0)){ 

        $array['rate'] = $array['rate']+1; 

    }
    
    if($array['alexa'] <= 10000){ 

        $array['rate'] = $array['rate']+5; 

    }
    else if(($array['alexa'] >= 10001) && ($array['alexa'] <= 30000)){ 

        $array['rate'] = $array['rate']+4; 

    }
    else if(($array['alexa'] >= 30001) && ($array['alexa'] <= 100000)){ 

        $array['rate'] = $array['rate']+3; 

    }
    else if(($array['alexa'] >= 100001) && ($array['alexa'] <= 2000000)){ 

        $array['rate'] = $array['rate']+2; 

    }
    else if($array['alexa'] >= 2000001){ 

        $array['rate'] = $array['rate']+1; 

    }
    
    $rate_age = explode(" ", $array['site_age']);

    $rate_age = $rate_age[0];

    if($rate_age>=7){ 

        $array['rate']=$array['rate']+5; 

    }
    else if(($rate_age >= 5) && ($rate_age <= 6)){ 

        $array['rate']=$array['rate']+4; 

    }
    else if(($rate_age >= 3) && ($rate_age <= 4)){ 

        $array['rate']=$array['rate']+3; 

    }
    else if(($rate_age >= 1) && ($rate_age <= 2)){ 

        $array['rate']=$array['rate']+2;

    }	
    else if($rate_age == 0){ 

        $array['rate']=$array['rate']+1; 

    }

    if($array['domain_lenght'] < 5) { 

        $array['rate'] = $array['rate']+5; 

    }
    
    if(($array['curl_info']['total_time'] >= 0.61) && ($array['curl_info']['total_time'] >= 0.85)){ 

        $array['pass']= $array['pass']+1; 

    }                    
    else{ 

        $array['fail']= $array['fail']+1; 

    }

    if($array['curl_info']['size_download'] <= 200){ 

        $array['pass']= $array['pass']+1; 

    }
    else { 

        $array['fail']= $array['fail']+1; 

    }	

    if($array['get_alt_miss'] == 0) {	

        $array['rate'] = $array['rate']+1;
        
        $array['pass']= $array['pass']+1;

    }
    else {

        $array['fail'] = $array['fail']+1;

    }
    
    if($array['check_heading'] == 1){

        $array['pass'] = $array['pass']+1;	
        
        $array['rate'] = $array['rate']+2;

    }
    else if(($array['check_heading'] > 1)&&($array['check_heading'] <= 5)){

        $array['fail'] = $array['fail']+1;
        
        $array['rate'] = $array['rate']+3;

    }
    
    if(!empty($array['title'])){

        $array['pass']= $array['pass']+1;	

        $array['rate'] = $array['rate']+2;	

    }
    else {

        $array['fail'] = $array['fail']+1;

    }

    if(!empty($array['description'])) {

        $array['pass']= $array['pass']+1;   

        $array['rate'] = $array['rate']+1;	

    }
    else {

        $array['fail']= $array['fail']+1;

    }				

    if($array['get_top_country_alexa'] !='null') {

        $array['rate'] = $array['rate']+5;

    }

    if($array['robot']['status'] == 200) {

        $array['rate'] = $array['rate']+2;

        $array['pass'] = $array['pass']+1;

    }
    else {

        $array['fail']= $array['fail']+1; 

    }
    
    return $array;
    
}

$setting["user"] = $user=getUser();

log_user($user);

?>
