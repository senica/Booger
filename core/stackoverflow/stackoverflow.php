<?php require(ASSETS.'/no_direct.php'); ?>
<?php
/*************************************
* Get info from Stackoverflow.com
* See http://api.stackoverflow.com/1.1/usage for documentation of the Stackoverflow api
*************************************/

$bg->add_shortcode('stackoverflow', 'core_stackoverflow_init', array(
	'firstcall'=>'core_stackoverflow_sitefoot'
));

function core_stackoverflow_sitefoot(){
	global $bg;
	$bg->add_css(URL.'/core/stackoverflow/style.css', 'site-foot');
}

//Much Thanks to patatraboum http://www.php.net/manual/en/function.gzinflate.php#77336
function core_stackoverflow_inflate($gzData){ 
    if(substr($gzData,0,3)=="\x1f\x8b\x08"){ 
        $i=10; 
        $flg=ord(substr($gzData,3,1)); 
        if($flg>0){ 
            if($flg&4){ 
                list($xlen)=unpack('v',substr($gzData,$i,2)); 
                $i=$i+2+$xlen; 
            } 
            if($flg&8) $i=strpos($gzData,"\0",$i)+1; 
            if($flg&16) $i=strpos($gzData,"\0",$i)+1; 
            if($flg&2) $i=$i+2; 
        } 
        return gzinflate(substr($gzData,$i,-8)); 
    } 
    else return false; 
}

function core_stackoverflow_init($obj){
	global $bg;
	$options = $obj->options;
	
	$options->query = html_entity_decode($options->query); //Need to change &amp; back to & for this purpose
	//Allow for Search Results with a $_GET['search'] = 'search value';
	if(!empty($_GET['search'])){ $options->resource = 'search'; $options->query .= '&intitle='.urlencode($_GET['search']); }
	if(!empty($_GET['id'])){ $options->resource = $options->resource.'/'.$_GET['id']; }
	if(!empty($_GET['pg'])){ $options->query .= '&page='.$_GET['pg']; }
	
	$fp = fsockopen("api.stackoverflow.com", 80, $errno, $errstr, 5);
	if (!$fp){ echo 'Could not connect to StackOverflow'; return false; }
	$out = "GET /1.1/".$options->resource."?".$options->query." HTTP/1.1\r\n";
	$out .= "Host: api.stackoverflow.com\r\n";
	$out .= "Accept-Encoding: gzip\r\n";
	$out .= "Connection: Close\r\n\r\n";
	fwrite($fp, $out);
	$buffer = '';
	while (!feof($fp)) {
		$buffer .= fgets($fp, 128);
	}
	fclose($fp);
	
	preg_match("/^content-length:\s*(\d+)\s*$/mi", $buffer, $matches);
	$length = $matches[1];	
	$content = substr($buffer, $length*-1, $length);
	
	$content = core_stackoverflow_inflate($content);
	$content = json_decode($content);
	
	if(!empty($options->template) && file_exists(SITE.'/'.$options->template)){
		require(SITE.'/'.$options->template);		
	}else{
		require(SITE.'/core/stackoverflow/template.php');	
	}
}
?>