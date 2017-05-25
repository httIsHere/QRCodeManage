<?php
//include("sql_connection.php");
$user = $_POST['user'];
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
	$sql = "select Pwd from YQ_ManageUser where ManageUserName='$user'";
//	if($result = mysqli_query($conn,$sql)){
	if($result = runSelectSql($sql)){
//		$rows = mysqli_fetch_array($result,MYSQLI_ASSOC);
//		echo $rows["Pwd"];
		echo $result[0]['Pwd'];
	}
	else{
		echo 0;
	}	
?>