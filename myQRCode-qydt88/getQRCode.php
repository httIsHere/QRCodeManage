<?php 
function downLoadQRCode($ticket,$fileName){
//$ticket = "gQEn8TwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyS2ZhcjF2ZkFid1QxMDAwME0wN1MAAgQ4gvlYAwQAAAAA";
$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);;
$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);    
    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec( $ch );
curl_close( $ch );
if(file_put_contents( "image/".$fileName, $res ) > 0)
return $fileName;
}
function downLoadQRCodeImg($ticket,$fileName){
$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".urlencode($ticket);;
$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 0);    
    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec( $ch );
curl_close( $ch );
return file_put_contents( $fileName, $res );
}
//echo downLoadQRCodeImg("https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=gQGB8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNEVveDBxZkFid1QxMDAwME0wN3gAAgQ9Iv9YAwQAAAAA","pic3.jpg");
// echo downLoadQRCode("gQGB8DwAAAAAAAAAAS5odHRwOi8vd2VpeGluLnFxLmNvbS9xLzAyNEVveDBxZkFid1QxMDAwME0wN3gAAgQ9Iv9YAwQAAAAA","h4u.jpg");
function getTicket($sceneId){
    $postData = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "'$sceneId'"}}}';
    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token="+$postData;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);    
    curl_setopt($ch, CURLOPT_NOBODY, 0);    //只取body头
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec( $ch );
    $ticket = $res['ticket'];
    return $ticket;
}
?>