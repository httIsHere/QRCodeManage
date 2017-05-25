<?php
	//连接数据库
//	include("sql_connection.php");
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	
	$user = $_POST['user'];
	$op = $_POST['op'];
	function checkApp($account, $appid, $apps){
		$nowtime=time();
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$apps;		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			$jsoninfo = json_decode($output, true);	
			$Token = $jsoninfo["access_token"];
			$expire= $jsoninfo["expires_in"];
			if($expire == '7200' && $Token != null){
				$nowtime=time();
				$sql = "insert into AccessToken(WXAccount,Token,getTime,expire) values('$account','$Token','$nowtime','$expire')";								
				$res = runInsertUpdateDeleteSql($sql);
				if($res){									
					return $Token;								
				 	logger($account.'_getAccess_Token','log/','success',"token".$token);
				 }
				 else{ 
				 	return 0;									
				 	logger($account.'_getAccess_Token','log/','error',"Mysql-query error:".$sql.mysql_error());
				 }	
			}
			else{
				return 0;
				 logger($account.'_getAccess_Token','log/','error',"appid or appsecret failed");
			}
	}
	
	if($op == 1){
		$sql = "select WeChatAccount, AppId, AppSecret from YQ_ManageUser where ManageUserName ='$user'";
		$result = runSelectSql($sql);
		$reply = array();
		$reply[] = $result[0];
		echo json_encode($reply);	 	
	}
	else if($op == 2){
		$account = $_POST['account'];
		$id = $_POST['appid'];
		$s = $_POST['apps'];
		$sql = "update YQ_ManageUser set WeChatAccount = '$account', AppId = '$id', AppSecret = '$s' where ManageUserName='$user'";
		$result = runInsertUpdateDeleteSql($sql);
		if($result){
			echo 1;
		}
	}
	else if($op == 3){
		$account = $_POST['account'];
		$appid = $_POST['appid'];
		$apps = $_POST['apps'];
		echo checkApp($account, $appid, $apps);
	}
?>