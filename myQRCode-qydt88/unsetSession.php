<?php
	$op = $_POST['op'];
	if($op == 1){
		echo "remove".$_SESSION['statistic'].$_SESSION['userInfor'];
		unset($_SESSION['statistic']);
		unset($_SESSION['userInfor']);
	}
	else session_unset();
?>