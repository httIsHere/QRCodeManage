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
				$qrCodeInfo=runSelectSql("select SceneID,SceneName,SceneDescription,SceneImg, SceneUrl,QRCodeImgFileName,Ticket from YQ_QRCode where ManageUserName='$webuser'");
				$replyStr=json_encode($qrCodeInfo);
				break;
		case 1:
				$unitName=$_POST['unitName']; $desp=$_POST['desp']; $unitAddress=$_POST['unitAddress']; 	$userName=$_POST['userName']; $tel=$_POST['tel']; 
				$ret=runInsertUpdateDeleteSql("INSERT INTO YQ_QRCode(SceneName , SceneDescription,SceneAddress ,SceneUserName, SceneNameTel ,ManageUserName ) VALUE ('$unitName','$desp','$unitAddress','$userName','$tel','$webuser')");
				$replyStr="信息已经保存";
				break;
		case 2:
				$unitName=$_POST['unitName']; $desp=$_POST['desp']; $sceneImg=$_POST['sceneImg']; 	$sceneUrl=$_POST['sceneUrl'];
				if(checkTheLink($sceneUrl) == 1){
				$account = $_POST['account']; $appId = $_POST['appId']; $appS = $_POST['appS'];
					//上传图片至微信拂去其
				$sceneImg = dirname(__FILE__)."/'$sceneImg";
				logger('iM_QRUser_Mid','log/','Log',"sceneImg=".$sceneImg);
				//获得token
				$token = getAccess_Token($account, $appId, $appS);
				uploadImg
				$ret=runInsertUpdateDeleteSql("INSERT INTO YQ_QRCode(SceneName , SceneDescription,SceneImg, SceneUrl,ManageUserName ) VALUE ('$unitName','$desp','$sceneImg','$sceneUrl','$webuser')");
				$replyStr="信息已经保存";
				$isNewUser=runSelectSql("select SceneID,QRCodeImgFileName from YQ_QRCode where (ManageUserName='$webuser') and (QRCodeImgFileName IS NULL  OR QRCodeImgFileName ='')");
							 
				$qrNumber=count($isNewUser);
				if($qrNumber==0) $replyStr="无新的信息，二维码已全部显示！";
				else
				{	for($i=0;$i<$qrNumber;$i++)
					{	$QRCodeImgFileName=$isNewUser[$i]['QRCodeImgFileName'];   
						if(!empty($QRCodeImgFileName)) $replyStr="二维码全部产生，刷新页面查看！";
						else
						{	
							$sceneID=$isNewUser[$i]['SceneID'];logger('iM_QRUser_Mid','log/','Log',"$sceneID"); 
							$qrCodeInfo=getTicketOfQrcode($account,$appId,$appS,$sceneID);
							$ticket=$qrCodeInfo['ticket'];
							$filename= getRandStr('qrcode_',36,'.jpg');
							$QRCodeImgFileName=getQRCodeUrlFromTicket($ticket,$filename);
//							$QRCodeImgFileName=downLoadQRCode($ticket,$filename);
							$ret=runInsertUpdateDeleteSql("update YQ_QRCode set Ticket='$ticket',QRCodeImgFileName='$QRCodeImgFileName' where (ManageUserName='$webuser') and (SceneID='$sceneID')");
						}
					}
					$replyStr="二维码生成成功！";
				}
				}
				else{
					$replyStr="图文链接为无效链接,请确认链接";
				}
				break;
		case 3:
				$img = $_POST['img'];
				$ticket = $_POST['ticket'];
				$sql = "delete from YQ_QRCode where QRCodeImgFileName = '$img' or Ticket = '$ticket'";
				$ret = runInsertUpdateDeleteSql($sql);
				$replyStr = "场景二维码已删除";
				break;
		case 4:
				$img = $_POST['img'];
				downloadImageFromWeiXin($img);
				break;
		case 5://编辑二维码信息
				
				
		default:
				$replyStr="switch default";
				break;					
	}
	
	
	 
	
	 					logger('iM_QRUser_Mid','log/','Log',"replyStr=".$replyStr);
	echo $replyStr;	
?>
