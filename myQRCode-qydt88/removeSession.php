<?php 
	session_start();
//	$_SESSION['statistic'] = null; 
//	$_SESSION['userInfor'] = null; 
	echo "remove".$_SESSION['statistic'].$_SESSION['userInfor'];
	unset($_SESSION['statistic']);
	unset($_SESSION['userInfor']);
?>