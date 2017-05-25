<?php
	header("Content-type: text/html; charset=utf-8"); 
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	session_start();
//if($_SESSION['statistic'] == null){
//	include("sql_connection.php");
	$user = $_POST['user'];
	$id = $_POST['appId'];
	$dateTime = $_POST['dateTime'];
	if(strstr($dateTime,'/') != "" || $dateTime == ""){
		$m = date("m");
		$d = date("d");
		$y = date("Y");
	}
	else{
		$dateStr = explode('-',$dateTime);
		$d = $dateStr[2];
		$m = $dateStr[1];
		$y = $dateStr[0];
	}
//	$user = "httishere@gmail.com";
//	$id = "ItsMusic";
//获得最近7天的数据(数据库内存放的是时间戳)
//select * from GlobalReceiveMsg where to_days(now()) - to_days(CreateTime) <= 7;
	//获得扫描用户数select distinct name from table
	$data = array();
	$i = 7;
//	$sql = "select count(distinct FromUserName) as c from GlobalReceiveMsg where to_days(now()) - to_days(CreateTime) <= 7";
	$userNum = array();
	for($i; $i > 0; $i --){
		$time = time();
		$start = mktime(-8,0,0,$m,$d-$i+1,$y);
//		$sql = "select count(distinct FromUserName) as c from GlobalReceiveMsg where WeChatAccount= '$id' and to_days(now()) - to_days(CreateTime) = '$i'";
//		$sql = "select distinct FromUserName from GlobalReceiveMsg where WeChatAccount= '$id' and to_days(now()) - to_days(CreateTime) = '$i'";
//		$sql = "select distinct FromUserName from GlobalReceiveMsg where WeChatAccount= '$id' and '$time' - CreateTime <= '$i'*24*60*60 and '$time' - CreateTime > ('$i'-1)*24*60*60";
		$sql = "select distinct OpenID from YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket where YQ_QRCode.ManageUserName = '$user' and WeChatAccount= '$id' and CreateTime - '$start' <= 24*60*60 and CreateTime - '$start' > 0";
//		$result = mysqli_query($conn, $sql);
//		$rows = mysqli_fetch_array($result,MYSQLI_ASSOC);
		$result = runSelectSql($sql);
		$num = count($result);
		$userNum[] = $num;
	}
	$data['userNum'] = $userNum;
	//获得扫描次数
	$i = 7;
	$msgNum = array();
	$sqlArray = array();
	for($i; $i > 0; $i --){
		$time = time();
		$start = mktime(-8,0,0,$m,$d-$i+1,$y);
//		$sql = "select FromUserName from GlobalReceiveMsg where WeChatAccount= '$id' and to_days(now()) - to_days(CreateTime) = '$i'";
//		$sql = "select FromUserName from GlobalReceiveMsg where WeChatAccount= '$id' and '$time' - CreateTime <= '$i'*24*60*60 and '$time' - CreateTime > ('$i'-1)*24*60*60";
		$sql = "select OpenID from YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket where YQ_QRCode.ManageUserName = '$user' and WeChatAccount= '$id' and CreateTime - '$start' <= 24*60*60 and CreateTime - '$start' > 0";
//		$result = mysqli_query($conn, $sql);
//		$rows = mysqli_fetch_array($result,MYSQLI_ASSOC);
//		$msgNum[] = $rows['c'];
		$sqlArray[] = $sql;
		$result = runSelectSql($sql);
		$num = count($result);
		$msgNum[] = $num;
	}	
//	$data['sqlArray'] = $sqlArray;
	$data['msgNum'] = $msgNum;
	//获得总扫描用户
//	$sql = "select distinct OpenID from (GlobalUser join GlobalReceiveMsg) where (OpenID = FromUserName) and OurWeChatAccount = '$id'";
	// $sql = "select distinct OpenID from YQ_ReceiveMsg where WeChatAccount= '$id'";
	$sql = "select distinct YQ_WXUser.OpenID from (YQ_WXUser join YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket) where YQ_QRCode.ManageUserName = '$user' and (YQ_WXUser.OpenID = YQ_ReceiveMsg.OpenID) and YQ_WXUser.WeChatAccount = '$id'";
//	$result = mysqli_query($conn, $sql);
//	$rows = mysqli_fetch_array($result,MYSQLI_ASSOC);
//	$data['totalUserNum'] = $rows['c'];
	$result = runSelectSql($sql);
	$num = count($result);
	$data['totalUserNum'] = $num;
	//获得总扫描次数
//	$sql = "select OpenID from (GlobalUser join GlobalReceiveMsg) where (OpenID = FromUserName) and OurWeChatAccount = '$id'";
	$sql = "select OpenID from YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket where YQ_QRCode.ManageUserName = '$user' and WeChatAccount= '$id'";
//	$result = mysqli_query($conn, $sql);
//	$rows = mysqli_fetch_array($result,MYSQLI_ASSOC);
//	$data['totalMsgNum'] = $rows['c'];
	$result = runSelectSql($sql);
	$num = count($result);
	$data['totalMsgNum'] = $num;
	//获得总二维码数
	$sql = "select SceneID from YQ_QRCode where ManageUserName='$user'";
//	$result = mysqli_query($conn, $sql);
//	$rows = mysqli_fetch_array($result,MYSQLI_ASSOC);
//	$data['totalQRNum'] = $rows['c'];
	$result = runSelectSql($sql);
	$num = count($result);
	$data['totalQRNum'] = $num;
	echo json_encode($data);
	$_SESSION['statistic'] = json_encode($data);
//}
//else{
//	echo $_SESSION['statistic'];
//}
?>