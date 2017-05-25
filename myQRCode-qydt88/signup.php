<?php
header('Access-Control-Allow-Origin:*');
header("Content-type: text/html; charset=utf-8"); 
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
include("page_switching.php");
define("yqAccount","ItsMusic");
define("yqAppId","wx3baa5a278d207f5e");
define("yqAppsecret","072b4d7cf04dd25660f577d0017c6f62");
$user=$_POST["email"]; 
$password=$_POST["password"];
//$user = "sbdjh";
//$password = "sjhdg";
// echo constant("yqAccount");
$sql = "insert into YQ_ManageUser(ManageUserName, Pwd, WeChatAccount, AppID, AppSecret) value('$user', '$password','".yqAccount."', '".yqAppId."','".yqAppsecret."')";
// echo $sql;
$ret=runInsertUpdateDeleteSql($sql);
if($ret)
page_redirect(false,"signin.html","注册成功，可进行登录！");
else
page_redirect(true,"","注册失败，该邮箱已被注册过，请重新注册！");
?>