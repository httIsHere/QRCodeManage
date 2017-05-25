<?php
	header("Content-type: text/html; charset=utf-8"); 
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	session_start();
//if($_SESSION['userInfor'] == null){
//	include("sql_connection.php");
	$user = $_POST['user'];
//	$account = 'ItsMusic';
	$account = $_POST['account'];
//	$account = "bemusic";
	$data = array();
	//先搜索出OpenID，再根据OpenID来查找nickname之类的？
//	 $sql = "select OpenID, max(CreateTime) from YQ_ReceiveMsg where WeChatAccount='ItsMusic' group by OpenID order by CreateTime desc";

	// $sql = "select distinct YQ_WXUser.OpenID,nickname,sex,city,headimgurl from (YQ_WXUser join YQ_ReceiveMsg) where (YQ_WXUser.OpenID = YQ_ReceiveMsg.OpenID) and YQ_WXUser.WeChatAccount = '$account'";
	// $sql = "select * from (select YQ_ReceiveMsg.OpenID,nickname,sex,city,headimgurl,max(CreateTime) as CreateTime from (YQ_WXUser join YQ_ReceiveMsg) where (YQ_WXUser.OpenID = YQ_ReceiveMsg.OpenID) and YQ_WXUser.WeChatAccount = '$account' group by YQ_ReceiveMsg.OpenID) as info order by CreateTime desc";
	$sql = "select * from (select YQ_ReceiveMsg.OpenID,nickname,sex,city,headimgurl,max(CreateTime) as CreateTime from (YQ_WXUser join YQ_ReceiveMsg join YQ_QRCode on YQ_QRCode.Ticket = YQ_ReceiveMsg.Ticket) where (YQ_WXUser.OpenID = YQ_ReceiveMsg.OpenID) and YQ_WXUser.WeChatAccount = '$account' and YQ_QRCode.ManageUserName = '$user' group by YQ_ReceiveMsg.OpenID) as info order by CreateTime desc";
	$link=openDB();
	$recodeList=array();
		if($link)
		{	$res=mysql_query($sql);		
			if(!$res) {logger('CommonFunction','log/','Error',"runSelectSql Mysql-query error:".$sql.mysql_error());}
			else 
			{	$sqlNum =  mysql_num_rows($res); 
				for($i=0;$i<$sqlNum;$i++)
				{	$row=mysql_fetch_array($res);  
					if($row) { 	
						$recodeList[$i]=$row;
					}
					}
			}
			$replyStr=json_encode($recodeList);	
			mysql_close($link);	
		}
//	$sql = "select headimgurl, nickname, sex, city from GlobalUser where OurWeChatAccount ='$account'";
//	$sql="select distinct FromUserName from GlobalReceiveMsg where WeChatAccount= '$account'";
//	$result = mysqli_query($conn, $sql);
//	$result = runSelectSql($sql);
//	for($i = 0; $i < count($result); $i++){
//		$openId = $result[$i]['FromUserName'];
//		$sql = 
//	}
//	$data['user'] = $result;
//	while($rows = mysqli_fetch_array($result,MYSQLI_ASSOC)){
//		$data[] = $rows;
//	}
//	echo json_encode($data);
	echo json_encode($recodeList);
//	echo $data;
	$_SESSION['userInfor'] = json_encode($result);
//	echo $data[1]['headimgurl'];
//}
//else{
//	echo $_SESSION['userInfor'];
//}
?>