<?php
	//连接数据库
//	include("sql_connection.php");
	require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	$user = $_POST['user'];
	$op = $_POST['op'];
	if($op == 1){
		$sql = "select Pwd from YQ_ManageUser where ManageUserName='$user'";
		if($result = runSelectSql($sql)){
			echo $result[0]['Pwd'];
		}
		else{
			echo 0;
		}	
	}
	else{
		$pwd = $_POST['pwd'];
		$sql = "update YQ_ManageUser set Pwd = '$pwd' where ManageUserName='$user'";
		if($result = runInsertUpdateDeleteSql($sql)){
			echo 1;
		}
		else{
			echo 0;
		}	
	}
?>