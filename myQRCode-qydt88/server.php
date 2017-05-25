<?php  
$img = isset($_POST['img'])? $_POST['img'] : '';  
$userId = $_POST['user'];
  
// 获取图片  
list($type, $data) = explode(',', $img);  
  
// 判断类型  
if(strstr($type,'image/jpeg')!=''){  
    $ext = '.jpg';  
}elseif(strstr($type,'image/gif')!=''){  
    $ext = '.gif';  
}elseif(strstr($type,'image/png')!=''){  
    $ext = '.png';  
}  
  
// 生成的文件名  
$photo = $userId.'_'.time().$ext;  
  
// 生成文件  
file_put_contents($photo, base64_decode($data), true);  
  
// 返回  
header('content-type:application/json;charset=utf-8');  
$ret = array('img'=>$photo);  
echo json_encode($ret);  
?>  