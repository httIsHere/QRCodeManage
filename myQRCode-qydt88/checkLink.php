<?php
function checkTheLink($url){
$ch = curl_init(); 
$timeout = 10; 
curl_setopt ($ch, CURLOPT_URL, $url); 
curl_setopt($ch, CURLOPT_HEADER, 1); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout); 
$contents = curl_exec($ch);
if($contents !== false){ 
  $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
  // echo $statusCode;
  if($statusCode == 200){ 
  	return 1;
  } 
  else if($statusCode < 400){ 
  	return 1; 
  }
  else{
  	return 0;
  }
}
else{
	return 0;
}
curl_close( $ch );
}
// function checkTheLink($url)
// {
// $check = @fopen($url,"r");
// if($check)
// $status = 1;
// else
// $status = 0;
// return $status;
// }
// echo checkTheLink("www.baidu.com");
?>