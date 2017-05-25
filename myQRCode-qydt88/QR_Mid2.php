<?php
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
include("getQRCode.php");
include("checkLink.php");
include("uploadImgToWX.php");
//$fromOpenID=$_POST['fromOpenID'];  
//$fromOpenID = "123";
 $type=(integer)$_POST['type'];
	//获得当前的user
	$webuser=$_POST['UserWebID'];
	switch($type)
	{
		case 0:
				// $qrCodeInfo=runSelectSql("select SceneID,SceneName,SceneDescription,SceneImg, SceneUrl,QRCodeImgFileName,Ticket,SceneImage from YQ_QRCode where ManageUserName='$webuser' order by SceneID desc");
				$qrCodeInfo=runSelectSql("select SceneID,SceneName,SceneDescription,SceneImg, Ticket,SceneImage,SceneUrl from YQ_QRCode where ManageUserName='$webuser' and Ticket is not null and Ticket <> '' order by SceneID desc");
				$replyStr=json_encode($qrCodeInfo);
				break;
		case 2:
				$unitName=$_POST['unitName']; 
				$desp=$_POST['desp']; 
				$sceneImg=$_POST['sceneImg']; 	
				$sceneUrl=$_POST['sceneUrl'];
				$sceneImage = $_POST['sceneImage'];
				$account = $_POST['account']; $appId = $_POST['appId']; $appS = $_POST['appS'];
				//如果链接不为空对链接进行判断
				$urlCheck = 1;
				if($sceneUrl != ""){
					//无效链接
					if(checkTheLink($sceneUrl) != 1){
						$urlCheck = 0;
						$replyStr="图文链接为无效链接,请确认链接";
						break;
					}
				}
				//如果有图片上传图片至微信服务器
				if($sceneImg != ""){
				$Img = dirname(__FILE__)."/".$sceneImg;
				logger('iM_QRUser_Mid','log/','Log',"sImg=".$Img);
				//获得token
				$token = getAccess_Token($account, $appId, $appS);
				logger('iM_QRUser_Mid','log/','Log',"token=".$token);
				if($token != null || $token != "")
				$url = uploadImg($token,$Img);
				logger('iM_QRUser_Mid','log/','Log',"sceneImg=".$url);
				if($url == null || $url == ""){
					$replyStr="图片上传失败！";
					break;
				}
				}

				if($urlCheck == 1){
				$sql = "INSERT INTO YQ_QRCode(SceneName , SceneDescription,SceneImg, SceneImage,SceneUrl,ManageUserName ) VALUE ('$unitName','$desp','$url','$sceneImg','$sceneUrl','$webuser')";
				logger('iM_QRUser_Mid','log/','Log',"save sql = "+$sql); 
				$ret=runInsertUpdateDeleteSql($sql);
				$replyStr="信息已经保存";
				$isNewUser=runSelectSql("select SceneID,QRCodeImgFileName from YQ_QRCode where (ManageUserName='$webuser') and (QRCodeImgFileName IS NULL  OR QRCodeImgFileName ='')");
							 
				$qrNumber=count($isNewUser);
				if($qrNumber==0) 
					$replyStr="无新的信息，二维码已全部显示！";
				else{	
					for($i=0;$i<$qrNumber;$i++){	
						$QRCodeImgFileName=$isNewUser[$i]['QRCodeImgFileName'];   
						if(!empty($QRCodeImgFileName)) 
							$replyStr="二维码全部产生，刷新页面查看！";
						else
						{	
							$sceneID=$isNewUser[$i]['SceneID'];logger('iM_QRUser_Mid','log/','Log',"$sceneID"); 
							$qrCodeInfo=getTicketOfQrcode($account,$appId,$appS,$sceneID);
							$ticket=$qrCodeInfo['ticket'];
							// echo $ticket;
							if($ticket != null && $ticket != ""){
							$filename= getRandStr('qrcode_',36,'.jpg');
							$QRCodeImgFileName=getQRCodeUrlFromTicket($ticket,$filename);
							logger('iM_QRUser_Mid','log/','Log',"ticket = '$ticket'"); 
							$ret=runInsertUpdateDeleteSql("update YQ_QRCode set Ticket='$ticket',QRCodeImgFileName='$QRCodeImgFileName' where (ManageUserName='$webuser') and (SceneID='$sceneID')");
							$replyStr="二维码生成成功！";
						}else{
							$replyStr = "无法获取二维码，请确认公众号信息";
						}
						}
					}
					break;
				}
				}
				break;
		case 3:
				$ticket = $_POST['ticket'];
				$sql = "delete from YQ_QRCode where Ticket = '$ticket'";
				$ret = runInsertUpdateDeleteSql($sql);
				$replyStr = "场景二维码已删除";
				break;
		case 4://编辑二维码信息
				$ticket = $_POST['ticket'];
				$account = $_POST['account']; $appId = $_POST['appId']; $appS = $_POST['appS'];
				$unitName=$_POST['unitName']; $desp=$_POST['desp'];$sceneUrl=$_POST['sceneUrl'];
				$sceneImg = $_POST['sceneImg'];
				//如果链接不为空对链接进行判断
				$urlCheck = 1;
				if($sceneUrl != ""){
					//无效链接
					if(checkTheLink($sceneUrl) != 1){
						$urlCheck = 0;
						$replyStr="图文连接为无效链接，场景二维码修改失败！";
						break;
					}
				}
				//如果有图片上传图片至微信服务器
				if($sceneImg != null  && $sceneImg != ""){
				$Img = dirname(__FILE__)."/".$sceneImg;
				logger('iM_QRUser_Mid','log/','Log',"sImg=".$Img);
				//获得token
				$token = getAccess_Token($account, $appId, $appS);
				logger('iM_QRUser_Mid','log/','Log',"token=".$token);
				if($token != null || $token != "")
				$url = uploadImg($token,$Img);
				logger('iM_QRUser_Mid','log/','Log',"sceneImg=".$url);
				if($url == null || $url == ""){
					$replyStr="图片上传失败！";
					break;
				}
				}
				if($url != null && $url != ""){
				// if(checkTheLink($sceneUrl) == 1){
					$sql = "update YQ_QRCode set SceneName='$unitName', SceneDescription='$desp', SceneUrl='$sceneUrl',SceneImage = '$sceneImg',SceneImg = '$url' where Ticket='$ticket'";
				}
				else{
					$sql = "update YQ_QRCode set SceneName='$unitName', SceneDescription='$desp', SceneUrl='$sceneUrl' where Ticket='$ticket'";
				}
				logger('iM_QRUser_Mid','log/','Log',"update sql = "+$sql); 
				$ret = runInsertUpdateDeleteSql($sql);
				$replyStr = "二维码修改成功！";
				// }
				// else{
				// 	$replyStr = "2";
				// }
				break;				
		default:
				$replyStr="switch default";
				break;					
	}
	logger('iM_QRUser_Mid','log/','Log',"replyStr=".$replyStr);
	echo $replyStr;	
?>