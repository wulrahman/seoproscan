<?php

class whois {

    public $ext = array(
    '.com' => array('whois.crsnic.net','No match for'),
    '.net' => array('whois.crsnic.net','No match for'),
    '.org' => array('whois.publicinterestregistry.net','NOT FOUND'),
    '.us' => array('whois.nic.us','Not Found'),
    '.biz' => array('whois.biz','Not found'),
    '.info' => array('whois.afilias.net','NOT FOUND'),
    '.eu' => array('whois.eurid.eu','FREE'),
    '.mobi' => array('whois.dotmobiregistry.net', 'NOT FOUND'),
    '.tv' => array('whois.nic.tv', 'No match for'),
    '.in' => array('whois.inregistry.net', 'NOT FOUND'),
    '.co.uk' => array('whois.nic.uk','No match'),
    '.co.ug' => array('wawa.eahd.or.ug','No entries found'),
    '.or.ug' => array('wawa.eahd.or.ug','No entries found'),
    '.sg' => array('whois.nic.net.sg','NOMATCH'),
    '.com.sg' => array('whois.nic.net.sg','NOMATCH'),
    '.per.sg' => array('whois.nic.net.sg','NOMATCH'),
    '.org.sg' => array('whois.nic.net.sg','NOMATCH'),
    '.com.my' => array('whois.mynic.net.my','does not Exist in database'),
    '.net.my' => array('whois.mynic.net.my','does not Exist in database'),
    '.org.my' => array('whois.mynic.net.my','does not Exist in database'),
    '.edu.my' => array('whois.mynic.net.my','does not Exist in database'),
    '.my' => array('whois.mynic.net.my','does not Exist in database'),
    '.nl' => array('whois.domain-registry.nl','not a registered domain'),
    '.ro' => array('whois.rotld.ro','No entries found for the selected'),
    '.com.au' => array('whois.ausregistry.net.au','No data Found'),
    '.ca' => array('whois.cira.ca', 'AVAIL'),
    '.org.uk' => array('whois.nic.uk','No match'),
    '.name' => array('whois.nic.name','No match'),
    '.ac.ug' => array('wawa.eahd.or.ug','No entries found'),
    '.ne.ug' => array('wawa.eahd.or.ug','No entries found'),
    '.sc.ug' => array('wawa.eahd.or.ug','No entries found'),
    '.ws' => array('whois.website.ws','No Match'),
    '.be' => array('whois.ripe.net','No entries'),
    '.com.cn' => array('whois.cnnic.cn','no matching record'),
    '.net.cn' => array('whois.cnnic.cn','no matching record'),
    '.org.cn' => array('whois.cnnic.cn','no matching record'),
    '.no' => array('whois.norid.no','no matches'),
    '.se' => array('whois.nic-se.se','No data found'),
    '.nu' => array('whois.nic.nu','NO MATCH for'),
    '.com.tw' => array('whois.twnic.net','No such Domain Name'),
    '.net.tw' => array('whois.twnic.net','No such Domain Name'),
    '.org.tw' => array('whois.twnic.net','No such Domain Name'),
    '.cc' => array('whois.nic.cc','No match'),
    '.nl' => array('whois.domain-registry.nl','is free'),
    '.pl' => array('whois.dns.pl','No information about'),
    '.pt' => array('whois.dns.pt','No match')
    );

    public $error;

    function available($domain) {
        
        $domain = trim($domain);
        
        if (preg_match('/^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)*[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?$/i',$domain) != 1) {
            
            $error = 'Invalid domain (Letters, numbers and hypens only) ('.$domain.')';

            return false;
            
        }
        
        preg_match('@^(http://www\.|http://|www\.)?([^/]+)@i', $domain, $preg_metch_result);
        
        $f_result = '';
        
        $domain = $preg_metch_result[2];
        
        $domain_name_array = explode('.', $domain);
        
        $domain_domain = strtolower(trim($domain_name_array[count($domain_name_array)-1]));
        
        $ext_in_list = false;

        if (array_key_exists('.'.$domain_domain, $this->ext)){
        
            $ext_in_list = true;
            
        }

        if(strlen($domain) > 0 && $ext_in_list){
            
            $server = '';
            
            $server = $this->ext['.' .$domain_domain][0];
            
            $lookup_result = gethostbyname($server);

            if ($lookup_result == $server){
                
                $error = 'Error: Invalid extension - '.$domain_domain.'. / server has outgoing connections blocked to '.$server.'.';

                return false;
                
            }

            $fs = fsockopen($server, 43,$errno,$errstr,10);

            if (!$fs || ($errstr != "")){
            
                $error = 'Error: ('.$server.') '.$errstr.' ('.$errno.')';
            
                return false;
                
            }

            fputs($fs, "$domain\r\n");
            
            while( !feof($fs) ) {
            
                $f_result .= fgets($fs,128);
                
            }

            fclose($fs);

            if($domain_domain == 'org'){

                nl2br($f_result);

            }

            if(preg_match("/".$this->ext['.'.$domain_domain][1]."/i", $f_result)){
                
                return true;
                
            } 
            else {
                
                return false;
                
            }

        } 
        else {

            $error = 'Invalid Domain and/or TLD server entry does not exist';

        }

        return false;
        
    }

}

class DomainAge {
    
  private $WHOIS_SERVERS=array(
  "com"               =>  array("whois.verisign-grs.com","/Creation Date:(.*)/"),
  "net"               =>  array("whois.verisign-grs.com","/Creation Date:(.*)/"),
  "org"               =>  array("whois.pir.org","/Created On:(.*)/"),
  "info"              =>  array("whois.afilias.info","/Created On:(.*)/"),
  "biz"               =>  array("whois.neulevel.biz","/Domain Registration Date:(.*)/"),
  "us"                =>  array("whois.nic.us","/Domain Registration Date:(.*)/"),
  "uk"                =>  array("whois.nic.uk","/Registered on:(.*)/"),
  "ca"                =>  array("whois.cira.ca","/Creation date:(.*)/"),
  "tel"               =>  array("whois.nic.tel","/Domain Registration Date:(.*)/"),
  "ie"                =>  array("whois.iedr.ie","/registration:(.*)/"),
  "it"                =>  array("whois.nic.it","/Created:(.*)/"),
  "cc"                =>  array("whois.nic.cc","/Creation Date:(.*)/"),
  "ws"                =>  array("whois.nic.ws","/Domain Created:(.*)/"),
  "sc"                =>  array("whois2.afilias-grs.net","/Created On:(.*)/"),
  "mobi"              =>  array("whois.dotmobiregistry.net","/Created On:(.*)/"),
  "pro"               =>  array("whois.registrypro.pro","/Created On:(.*)/"),
  "edu"               =>  array("whois.educause.net","/Domain record activated:(.*)/"),
  "tv"                =>  array("whois.nic.tv","/Creation Date:(.*)/"),
  "travel"            =>  array("whois.nic.travel","/Domain Registration Date:(.*)/"),
  "in"                =>  array("whois.inregistry.net","/Created On:(.*)/"),
  "me"                =>  array("whois.nic.me","/Domain Create Date:(.*)/"),
  "cn"                =>  array("whois.cnnic.cn","/Registration Date:(.*)/"),
  "asia"              =>  array("whois.nic.asia","/Domain Create Date:(.*)/"),
  "ro"                =>  array("whois.rotld.ro","/Registered On:(.*)/"),
  "aero"              =>  array("whois.aero","/Created On:(.*)/"),
  "nu"                =>  array("whois.nic.nu","/created:(.*)/")
  );
    
  public function age($domain) {
      
      $domain = trim($domain); //remove space from start and end of domain
      
      if(substr(strtolower($domain), 0, 7) == "http://") {

          $domain = substr($domain, 7); // remove http:// if included

      }
      if(substr(strtolower($domain), 0, 4) == "www.") {

          $domain = substr($domain, 4);//remove www from domain

      }

      if(preg_match("/^([-a-z0-9]{2,100})\.([a-z\.]{2,8})$/i",$domain)) {

          $domain_parts = explode(".", $domain);

          $tld = strtolower(array_pop($domain_parts));

          if(!$server=$this->WHOIS_SERVERS[$tld][0]) {

              return false;

          }

          $res=$this->queryWhois($server,$domain);

          if(preg_match($this->WHOIS_SERVERS[$tld][1],$res,$match)){

              date_default_timezone_set('UTC');

              $time = time() - strtotime($match[1]);

              $years = floor($time / 31556926);

              $days = floor(($time % 31556926) / 86400);

              if($years == "1") {

                  $y= "1 year";

              }
              else {

                  $y = $years . " years";

              }

              if($days == "1") {

                  $d = "1 day";

              }
              else {

                  $d = $days . " days";

              }

              return "$y, $d";


          }
          else {

              return false;

          }

      }  
      else {

        return false;

      }

  }
      
  private function queryWhois($server,$domain) {
  
      $fp = @fsockopen($server, 43, $errno, $errstr, 20) or die("Socket Error " . $errno . " - " . $errstr);

      if($server=="whois.verisign-grs.com") {
    
          $domain="=".$domain;
      
      }
      
      fputs($fp, $domain . "\r\n");
      
      $out = "";
      
      while(!feof($fp)){
          
          $out .= fgets($fp);
          
      }
      
      fclose($fp);
      
      return $out;
      
      }
    
}

$w=new DomainAge();

function build_stats($input,$num) {
    
	$results = array();
	
	foreach ($input as $keyz=>$word) {
        
		$phrase = '';
		
		for ($i=0;$i<$num;$i++) {
            
			if ($i!=0) $phrase .= ' ';
            
			$phrase .= strtolower( $input[$keyz+$i] );
            
		}
        
		if (!isset( $results[$phrase])) {
            
			$results[$phrase] = 1;
            
        }
		else {
            
			$results[$phrase]++;
            
        }
        
	}
    
	if ($num == 1) {
        
		//clean boring words
		$a = explode(" ","the of and to a in that it is was i for on you he be with as by at have are this not but had his they from she which or we an there her were one do been all their has would will what if can when so my");
        
		foreach ($a as $banned) unset($results[$banned]);
        
	}
	
	//sort, clean, return
	array_multisort($results, SORT_DESC);
    
	unset($results[""]);
    
	return $results;
    
}


class shareCount {

    private $url,$timeout;
    
    function __construct($url,$timeout=10) {
        
        $this->url=rawurlencode($url);
        $this->timeout=$timeout;
        
    }

    function get_plusones()  {
        
        $curl = curl_init();
        
        curl_setopt($curl, CURLOPT_URL, "https://clients6.google.com/rpc");
        
        curl_setopt($curl, CURLOPT_POST, true);
        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
        curl_setopt($curl, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"'.rawurldecode($this->url).'","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        
        $curl_results = curl_exec ($curl);
        
        curl_close ($curl);
        
        $json = json_decode($curl_results, true);
        
        return isset($json[0]['result']['metadata']['globalCounts']['count'])?intval( $json[0]['result']['metadata']['globalCounts']['count'] ):0;
        
    }

    
}
?>
