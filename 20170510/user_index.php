<?php
require_once(dirname(_FILE_).'../cgi-bin/CommonFunction.php');
session_start();
include("page_switching.php");

$accessid = $_SESSION["accessID"];
$user=$_SESSION["username"];
$pwd = $_SESSION["userpwd"];
if($_SESSION["accessID"] == null){
	page_redirect(false,"signin.html","请重新登录");
}
$accessid = $_SESSION["accessID"];
$user=$_SESSION["username"];
$pwd = $_SESSION["userpwd"];
$sql = "SELECT ManageUserName, AccessID FROM YQ_ManageUser WHERE ManageUserName = '$user'";
$result = runSelectSql($sql);
if($result){
	if($result[0]["AccessID"] != $accessid){
       page_redirect(false,"signin.html","请重新登录");
	}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php echo $user;?>的主页</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style3.css">
    <link rel="stylesheet" href="userIndexStyle.css">
	<link rel="stylesheet" type="text/css" href="DateTimePicker.css" />
    <link type="text/css" rel="stylesheet" href="fileinput.css" />
    <script src="https://code.jquery.com/jquery.js"></script>
	<script type="text/javascript" src="fileinput.js"></script>
	<script type="text/javascript" src="zh.js"></script>
    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
            <!--图表引入-->
	<script src="//cdn.bootcss.com/Chart.js/2.5.0/Chart.bundle.js"></script>
	<script src="//cdn.bootcss.com/Chart.js/2.5.0/Chart.bundle.min.js"></script>
	<script type="text/javascript" src="DateTimePicker.js"></script>
	
	<style type="text/css">
		.alert{
			position: absolute;
			width: 30%;
			top: 40%;
			left: 35%;
			z-index: 100;
		}
		.closeBtn:hover{
			cursor: pointer;
		}
		#resultList tr td{
			text-align: center;
		}
		#sceneImg{
			width: 100%;
			background-color: rgba(255,255,255,0.3);
			height: 30px;
			line-height: 30px;
			padding-left: 5px;
			padding-right: 15px;
			outline: none;
			border-radius: 5px;
			border: 1px solid rgba(0,0,0,0.3);
			margin: 10px 0 10px 0;
		}
	</style>
</head>
<body onbeforeunload="removeSession()">
<div class="mainHead">
	<div class="userName"><?php echo $user;?></div>
	<div class="signout">退出</div>
</div>
<div class="container">
	<div class="welcomeBar">
	</div> 
	<div class="row main-nav">
		<!-- 侧边列表 -->
		<div class="nav-box statistic"></div>
		<div class="nav-box qr-icon"></div>
		<div class="nav-box setting"></div>
		<div class="nav-box operation"></div>
	</div>
	<!-- 功能页 -->
	<!-- 数据统计 -->
	 <div class="contentPage myStatisticIndex">
	 	<div class="row">
	 	<ul id="statisticTab" class="nav nav-tabs col-md-11 col-xs-11">
			<li class="active"><a href="#StatisticUserNum" data-toggle="tab">扫描图表</a></li>
			<li><a href="#StatisticUserInfo" data-toggle="tab">扫描用户列表</a></li>
			<li><a href="#StatisticNum" data-toggle="tab">扫描统计</a></li>
			<li><a href="#StatisticQRNum" data-toggle="tab">场景数量</a></li>
		</ul>
	 	<div class="col-md-1 col-xs-1 closeBtn text-right" style="padding-right: 0;">
	 		<img src="close.png" style="width: 15px;height: 15px;float: right;margin-bottom: 5px;"/>
	 	</div>
		</div>
    	<br />
    	<div id="statisticTabContent" class="tab-content">
    	<!--显示统计数据图表-->
    	<div class="row showChart statisticShow tab-pane fade in active" id="StatisticUserNum">
    		<!--<div class="col-md-12" style="color: #000000;">近7天的数据统计</div>-->
    		<div class="col-md-12">
    			<p>选择日期(默认到今天的近7天扫描记录)</p>
    			<input type="text"class="timePickerStyle" data-field="date" data-format="yyyy-MM-dd" readonly id="dateInput">
    			<input type="button" name="dateBtn" class="btn1" id="dateBtn" value="确定" onclick="getStatisticData()"/>
    			<div id="datepicker"></div>
    		</div>
    		<canvas id="myChart" width="400" height="200"></canvas>
    	</div>
    	<!--显示扫描用户信息-->
    	<div class="row userInfo statisticShow tab-pane fade " id="StatisticUserInfo">
    		<input type="text" name="searchUser" id="searchUser" placeholder="查找用户" class="form-control" />
    		<br />
    		<table class="table table-hover table-striped text-center userInforTable">
    		</table>
    	</div>
    	<!--显示所有数据-->
    	<div class="row totalData statisticShow tab-pane fade" id="StatisticNum">
    		总扫描用户：<span></span>
    		<hr />
    		扫描总次数：<span></span>
    	</div>
    	<!--显示所产生的二维码的数量-->
    	<div class="row statisticShow tab-pane fade" id="StatisticQRNum">
    		总二维码数：<span id="QRNum"></span>
    	</div>
    	<div class="row">
    		<input type="button" class="col-xs-2 btn1 btn-login pull-left" value="刷新" id="uploadBtn">
    		<input type="button" class="col-xs-2 btn1 btn-register pull-right closeBtnTwo" value="关闭">
    	</div>
    	</div>
	</div>
    <!--信息浏览-->
    <!-- 二维码生成 -->
	<div class="contentPage myQRCodeCreateIndex">
	 	<div class="row">
	 	<ul id="myTab" class="nav nav-tabs col-md-11 col-xs-11">
			<li class="active"><a href="#QRCodeList" data-toggle="tab">已生成二维码</a></li>
			<li><a href="#CreateQRCode" data-toggle="tab">二维码生成</a></li>
		</ul>
	 	<div class="col-md-1 col-xs-1 closeBtn text-right" style="padding-right: 0;">
	 		<img src="close.png" style="width: 15px;height: 15px;float: right;margin-bottom: 5px;"/>
	 	</div>
		</div>
    	<br />
    	<div id="myTabContent" class="tab-content">    	
		<div class="row QRlist tab-pane fade in active" id="QRCodeList">
    		<div class="h3 text-info text-center">信息列表</div>
    		<table class="table table-bordered table-hover" width="95%" border="1" id="resultList">
                <tr class="sceneNameText" id="listTitle">
                	<th class="text-center">场景编号</th>
                    <th class="text-center">标题</th>
                    <th class="text-center">简介</th>
                    <th class="text-center">场景图片</th>
                    <th class="text-center">图文链接</th>
                    <th class="text-center">二维码</th>
                    <th class="text-center">操作</th>
                </tr>
			</table>
		</div>
		<div class="QRInput tab-pane fade" id="CreateQRCode">
    		<ul>
        		<li>
                    <div>标题<span class="redText">(必填*)</span></div>
            		<input type="text" id="unitName" class="form-control"/>
        		</li>
        		<li>
            		<div>简介（地址，联系人，联系电话）</div>
            		<textarea id="desp" rows="4" class="form-control"></textarea>
        		</li>
                    <div><strong>场景图片</strong></div>
            		<input type="file" id="sceneImg"/>
            		<p>支持jpg，png，gif格式图片</p>
            		<br />
        		<!--<li>
                    <div>场景图片<span class="redText">(必填*)</span></div>
            		<input type="file" id="sceneImg" multiple class="file"/>
        		</li>-->
        		<li>
            		<div>图文链接<span class="redText">(必填*)</span></div>
            		<input type="text" id="sceneUrl" class="form-control" />
        		</li>
<!--         		<li>
            		<div>联系电话</div>
            		<input type="text" id="tel" class="form-control" />
        		</li> -->
    		</ul>
		<div class="row">
    		<input type="button" class="btn1 btn-login" value="生成" onclick="checkInfo()"/>
    		<input type="button" class="col-xs-2 btn1 btn-register pull-right closeBtnTwo" value="关闭">
		</div>
		</div>
	</div>
	</div>
    <!-- 系统设置 -->
    <div class="contentPage mySystemSettingIndex">
	 	<div class="row">
	 	<ul id="myTab" class="nav nav-tabs col-md-11 col-xs-11">
			<li class="active"><a href="#ChangeAccountP" data-toggle="tab">绑定/修改公众号</a></li>
			<li><a href="#ChangePwdP" data-toggle="tab">修改登录密码</a></li>
		</ul>
	 	<div class="col-md-1 col-xs-1 closeBtn text-right" style="padding-right: 0;">
	 		<img src="close.png" style="width: 15px;height: 15px;float: right;margin-bottom: 5px;"/>
	 	</div>
		</div>
    	<br />
    	<div id="myTabContent" class="tab-content">
    	<!--有修改所操作的公众号和修改登录密码的修改-->
    	<div class="row changeApp settingChange tab-pane fade in active" id="ChangeAccountP">
    		<div class="form-group">
				<label for="account">微信公众号账号</label>
				<input type="text" id="Account" class="form-control">
			</div>
    		<br />
    		<div class="form-group">
				<label for="appID">AppID</label>
				<input type="text" id="AppId" class="form-control">
			</div>
    		<br />
			<div class="form-group">
				<label for="appSecret">AppSecret</label>
				<input type="text" id="AppSecret" class="form-control">
			</div>
    		<br />
			<!--<div class="form-group">-->
				<input type="button" id="change" value="确认" class="btn1 btn-login pull-left">
				<input type="button" id="reset" value="恢复" class="btn1 btn-register pull-left cancelBtn">
    			<input type="button" class="btn1 btn-login pull-right closeBtnTwo" style="float: right;" value="关闭">
			<!--</div>-->
    	</div>
    	<!--<hr />-->
    	<div class="row changePwd settingChange tab-pane fade" id="ChangePwdP">
    		<div class="form-group">
				<label for="appID">原密码</label>
				<input type="password" id="oldSecret" class="form-control">
			</div>
    		<br />
			<div class="form-group">
				<label for="appSecret">新密码</label>
				<input type="password" id="newSecret" class="form-control">
			</div>
    		<br />
			<div class="form-group">
				<label for="appSecret">确认新密码</label>
				<input type="password" id="checkNewSecret" class="form-control">
			</div>
    		<br />
				<input type="button" id="changepwd" value="确认" class="btn1 btn-login pull-left">
				<div class="col-xs-6"></div>
				<input type="button" id="reset" value="恢复" class="btn1 btn-register pull-right">
    	</div>
    </div>
    </div>
	<!--主页在这里-->
<!--基本页介绍-->
    <div class="contentPage pageIntroduction">
        <div class="row closeBtn">
            <img src="close.png" style="width: 15px;height: 15px;float: right;margin-bottom: 5px;"/>
        </div>
        <div class="row content-padding">
            <p class="introTitle">系统说明</p>
            <p class="introContent">该系统主要用于公众号场景二维码的生成与管理，其中场景二维码即带不同场景信息的二维码，用户扫描后，可以在公众号中接收到二维码内带有的场景信息；</p>
            <p class="introContent">可以进行扫描的统计与查看，查看二维码被扫描的次数以及扫描用户的信息；</p>
            <p class="introContent">系统界面左侧是四个导航目录</p>
        </div>
        <div class="row content-padding">
            <div class="introContentTitle">统计数据</div>
            <div class="introContentBox">
                <p>用于扫描的统计与查看（一共有四个标签页）；</p>
                <div class="col-md-12 col-xs-12"><img src="op1.JPG" style="width: 50%;"></div>
                <p>点击第一个标签页可查看当前扫描的统计图表，统计图表以一周为一个周期，默认显示最近到该天一周时间内的扫描数量，包括扫描用户量和全部扫描次数，可点击时间选框选择需要查看的时间并点击确定即可查看统计数据；</p>
                <p>点击第二个标签页可查看全部扫描用户数量和全部扫描次数；</p>
                <p>点击第三个标签页可查看当前用户所生成的场景二维码的数量；</p>
                <p>点击第四个标签页可查看并搜索扫描用户信息。</p>
            </div>
        </div>
        <div class="row content-padding ">
            <div class="introContentTitle">二维码管理</div>
            <div class="introContentBox">
                <p>用于二维码生成以及二维码查看（注意：二维码生成一定要绑定公众号的AppID和 AppSecret，公众号的AppID和AppSecret在微信公众平台>>开发>>基本配置）;</p>
                <p>第一个标签页是二维码内所带信息的输入以及二维码的生成，其中场景名称和场景地址是必填项目；</p>
                <p>第二个标签页是已生成的二维码的查看，可对二维码进行删除。</p>
            </div>
        </div>
        <div class="row content-padding">
            <div class="introContentTitle">系统设置</div>
            <div class="introContentBox">
            <p>主要用于用户所绑定公众号账户的操作以及登录密码的修改；</p>
            <p>第一个标签页是公众号账户的绑定与修改，在账号，AppID和AppSecret通过验证后只有修改账号才可以修改AppID和AppSecret，修改AppID和AppSecret后确认时会进行两者的验证只有验证通过才能正确修改；在确认修改之前点击取消按钮可恢复到修改之前的数据；</p>
            <p>第二个标签页是登录密码的修改，需要同时确认旧密码和新密码以及确认新密码，只有全部符合才能正确修改。</p>
            </div>
        </div>
    </div>
</div>
<!-- 模态框（Modal）二维码信息确认 -->
<div class="modal fade" id="checkQRInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">信息确认</h4>
            </div>
            <div class="modal-body" id="nowQRInfo"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="createBtn" onclick="createQRCodes()">确定</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<!-- /.modal -->
<!-- 模态框（Modal）公众号修改 -->
<div class="modal fade" id="checkChangeApp" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">是否确认修改</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="createBtn" onclick="realChangeApp()">确定</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<!-- /.modal -->
<!-- 模态框（Modal）二维码删除 -->
<div class="modal fade" id="checkDeleteQRCode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">确定删除二维码？</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="createBtn" onclick="deleteQRCode(codeIndex,codeImg,codeTicket)">确定</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<!-- /.modal -->
<div class="modal fade" id="alertTip" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="alertTipTitle"></h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">确定</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
<!-- /.modal -->
<!--信息编辑-->
<div class="modal fade" id="editSceneInfo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="color: #000000;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">修改信息</h4>
            </div>
            <div class="modal-body">
                    <div align="left">标题<span class="redText">(必填*)</span></div>
            		<input type="text" id="unitName" class="form-control"/>
            		<div align="left">简介（地址，联系人，联系电话）</div>
            		<textarea id="desp" rows="4" class="form-control"></textarea>
                    <div align="left">场景图片<span class="redText"></span></div>
            		<!--<input type="file" id="sceneImg"/>-->
            		<img src="" />
            		<div align="left">图文链接<span class="redText">(必填*)</span></div>
            		<input type="text" id="sceneUrl" class="form-control" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="createBtn" onclick="editQRCodeInfo()">确定</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal -->
</div>
</body>
<script type="text/javascript" src="/cgi-bin/commonJS.js"> </script>
<script type="text/javascript">
	var userId = "<?php echo $user; ?>";
	localStorage['userId'] = userId;
	console.log(userId);
	var nowIndex = 0;
	var fun1, fun2, fun3, fun4;//是否已点击过功能块
	var res = 0;
	//用于重置
	var account, id, secret;
	//默认头像
	var head = "head.jpeg";
	//删除code所需变量
	var codeIndex,codeImg,codeTicket;
	//生成二维码所需
	var sceneImg;
	init();
	changeFunc();
	//初始页面
	function init(){
		$('#datepicker').DateTimePicker();
		$('#dateInput').val(new Date().toLocaleDateString());
//		$('#datepicker').val(new Date());
		nowIndex = 0;
		//除了导航语其他都不出现
		for(var i = 1; i < $(".contentPage").length; i++){
			$(".contentPage").css('transform', 'scale(0.0)');
		}
		$("#statisticTab li").attr("class","");
		$("#statisticTab li").eq(0).addClass("active");
		$("#statisticTabContent .row").removeClass("active");
		$("#statisticTabContent .row").removeClass("in");
		$("#statisticTabContent .row").eq(0).addClass("in");
		$("#statisticTabContent .row").eq(0).addClass("active");
		getChart();
		// $(".statisticShow").eq(0).show();
//		$(".settingChange").eq(1).hide();
		getApp();
		whileAccountChange();
		//退出功能
		$(".signout").click(function(){
			signOut();
		});
		//关闭功能
		$('.closeBtn').click(function(){
			closeContentPage();
		});
		$('.closeBtnTwo').click(function(){
			closeContentPage();
		});
		
	}	
	//显示在窗口中央
	function goCenter(){
				var h = $(window).height();  
                var w = $(window).width();  
                var st = $(window).scrollTop();  
                var sl = $(window).scrollLeft();  
  				var top = h/10;
  				var left = w/5;
                $(".contentPage").css("top", top);  
                $(".contentPage").css("left", left);  
		
	}
	//导航切换
	function changeFunc(){
		$(".nav-box").click(function(){
			init();
			nowIndex = $(this).index();
			goCenter();
			$(".contentPage").eq(nowIndex).css('transform', 'scale(1.0)');
			// $(".settingChange").eq(0).show();
			getInfomation(nowIndex+1);
		});
		$('#uploadBtn').click(function(){
			removeSession();
			getStatisticData();
			getUserInfor();
		});
	}
	//加载相应数据（from session）
	function getInfomation(index){
		//如果session内有数据则直接读取否则获取数据并存入session
		//根据index判断读取数据
		switch(index){
			case 1:
				getStatisticData();
				getUserInfor();
//				getQRCodeNumber(userId);
				break;
			case 2:
				getInfos();
				break;
			case 3:
				//加载第四个功能块的数据
				getApp();
				changeApp();
				changePwd();
				break;
		}
	}
//----------------------文件上传输入框初始化----------------
//初始化fileinput控件（第一次初始化）
function initFileInput(ctrlName, uploadUrl) { 
 var control = $('#' + ctrlName); 
 control.fileinput({
 language: 'zh', //设置语言
 uploadUrl: uploadUrl, //上传的地址
 allowedFileExtensions : ['jpg', 'png','gif'],//接收的文件后缀
 showUpload: false, //是否显示上传按钮
 showCaption: true,//是否显示标题
 browseClass: "btn btn-warning", //按钮样式 
 dropZoneEnabled: false,//是否显示拖拽区域
 previewFileIcon: "<i class='glyphicon glyphicon-king'></i>", 
 });
}
//初始化fileinput控件（第一次初始化）
initFileInput("sceneImg", "");
//----------------------弹出警告框-----------------------
function alertShow(s){
	$('#alertTipTitle').html(s);
	$('#alertTip').modal('show');
	setTimeout(function(){
		$('#alertTip').modal('hide');
	},2000);
}
//----------------------数据统计-------------------------
	//得到数据统计的数据
	function getStatisticData(){
		var time = $('#dateInput').val();
		console.log(time);
		$.ajax({
			type:"post",
			url:"getStatisticData.php",
			dataType:"json",
			data:{appId:$("#Account").val(),user:userId,dateTime:time},
			success:function(data){
				var res = eval(data);
				console.log(res);
				if(res != null){
					getChart(res['userNum'], res['msgNum']);
					$('.totalData').children().eq(0).text(res['totalUserNum']);
					$('.totalData').children().eq(2).text(res['totalMsgNum']);
					$("#QRNum").text(res['totalQRNum']);
				}
			}
		});
	}
	//计算前一天,使图表地x轴动态显示
	function preDay(now){
		var l = new Array();
		if(now.indexOf("-") >= 0)
		var data = now.split("-");
		else var data = now.split("/");
		var year = data[0];
		var month = data[1];
		var day = data[2];
		var dd = new Date();
		for(var i = 6; i >= 0;i--){
		var d = new Date(year, month, day);
		dd.setTime(d.getTime()-24*60*60*1000*i);
  		var y = dd.getFullYear();
    	var m = dd.getMonth();
    	var d = dd.getDate();
    	// console.log(([y,m,d].join('-')));
    	l.push(([y,m,d].join('-')));
    	}
    	return l;
	}
	//chart
	function getChart(user, msg){
		var now = $('#dateInput').val();
		var label = preDay(now);
		var data = {
				labels : label,
				datasets : 	[
							{
								backgroundColor: 'rgba(255,165,121,.5)',
								borderColor: 'rgba(255,165,121,.5)',
								fill : true,
								data : user,
								label:"扫描用户量"
							},
							{
								fill: true,
								backgroundColor: 'rgba(0,120,255,.5)',
								borderColor: 'rgba(0,120,255,.5)',
								data : msg,
								label:"扫描次数"
							}
							]
				};

		var ctx = $("#myChart").get(0).getContext("2d");
		var options = {scales: {yAxes: [{ticks: {beginAtZero: true,callback: function(value) {if (value % 1 === 0) {return value;}}}}]}};
		var myBarChart = new Chart(ctx,{type:'bar', data:data,options:options});
//		new Chart(ctx).Bar(data);
	}
	//时间格式化
	function formatMyTime(str){
		var d = new Date(str*1000);
		commonTime = d.toLocaleString('chinese',{hour12:false});
		return commonTime;
	}
	//得到扫描用户信息
	function getUserInfor(){
		var info = null;
		$(".userInforTable").html("<tr><td>头像</td><td>用户名</td><td>性别</td><td>城市</td><td>最近扫描时间</td></tr>");
		$.ajax({
			type:"post",
			url:"getUserInfor.php",
			dataType:"json",
			data:{account:$("#Account").val()},
			success:function(data){
				var res = eval(data);
				var s,h;
				if(res != null){
					console.log(res);
					info = res;
					for(var i = 0; i < res.length; i++){
						h = res[i].headimgurl;
						if(res[i].headimgurl == ""){
							h = head;
						}
						var l = "<tr><td><img src='" + h + "' style='width:50px; height:50px;'/></td>";
						if(res[i].nickname == "")
							l += "<td><div class='userInfoLine'>公众号用户</div></td>";
						else
							l += "<td><div class='userInfoLine'>" + res[i].nickname +"</div></td>";
						if(res[i].sex == '2')
							s = "女";
						else if(res[i].sex == '1'){
							s = "男";
						}
						else s = "不明";
						l += "<td><div class='userInfoLine'>" + s +"</div></td>" + "<td><div class='userInfoLine'>" + res[i].city +"</div></td>";
						//将时间戳转换成日期
						var d = new Date(res[i]["max(CreateTime)"]);

						l += "<td><div class='userInfoLine'>" + formatMyTime(parseInt(res[i].CreateTime)) + "</div></td></tr>"
//						alert(l);
						$(".userInforTable").append(l);
					}
				}
			}
			
		});
		//监听input
		$('#searchUser').bind('input propertychange', function() { 
 			//进行相关操作 
 			var v = $('#searchUser').val();
 			var s;
			$(".userInforTable").html("<tr><td>头像</td><td>用户名</td><td>性别</td><td>城市</td></tr>");
			var l = "";
 			for(var i = 0; i < info.length; i++){
   				if(info[i].nickname.indexOf(v) >= 0){
   					l = "<tr><td><img src='" + info[i].headimgurl + "' style='width:50px; height:50px;'/></td>";
					l += "<td><div class='userInfoLine'>" + info[i].nickname +"</div></td>";
					if(info[i].sex == '2')
							s = "女";
						else if(info[i].sex == '1'){
							s = "男";
						}
						else s = "不明";
					l += "<td><div class='userInfoLine'>" + s +"</div></td>" + "<td><div class='userInfoLine'>" + info[i].city +"</div></td><tr>";
					$(".userInforTable").append(l);
   				}
 			}
		});
	}
//-----------------------二维码生成-----------------------
	//获得图片对象并上传
	function getFileAndUpload(){
		var img = $('#sceneImg').get(0).files[0];
		if(!img){  
            return;  
        }    
        if(!(img.type.indexOf('image')==0 && img.type && /\.(?:jpg|png|gif)$/.test(img.name)) ){
            return;  
        }
        var reader = new FileReader();  
        reader.readAsDataURL(img);  
  
        reader.onload = function(e){ // reader onload start  
            // ajax 上传图片  
            $.post("server.php", { img: e.target.result, user:userId},function(ret){  
                if(ret.img!=''){  
                	sceneImg = ret.img;
                	var unitName = $('#unitName').val();
    				var desp = $('#desp').val();
    				var sceneUrl = $('#sceneUrl').val();
    				var l = "<p>标题："+unitName+"</p><p>简介："+desp+"</p><p>场景图片：<img src="+ret.img+" style='height:15vw;width:50%;margin:10px auto;' /></p><p>图文链接："+sceneUrl+"</p>";
    				$("#nowQRInfo").html(l);
    				$("#checkQRInfo").modal('show');
    				}
                else{  
                    alertShow("所选图片出现问题");
                }  
            },'json');  
        } // reader onload end  
	}
        
    function listshow(xmlDoc)
    {   
        // alert("test!");
        // var l="";
        var l = '<tr class="text-center" id="listTitle"><th class="text-center">场景编号</th><th class="text-center">标题</th><th class="text-center">简介</th><th class="text-center">场景图片</th><th>图文链接</th><th class="text-center">二维码</th><th class="text-center">操作</th></tr>';
        $("#resultList").html(l);
        var field=eval(xmlDoc);
        if(field.length>0)
        {   
//      	alert(field.length);
        	for(var i=0;i<field.length;i++)
            {   
            	SceneName=field[i].SceneName;               SceneDesp=field[i].SceneDescription;  
                SceneImg=field[i].SceneImg;               SceneUrl=field[i].SceneUrl;
                SceneID = field[i].SceneID;
				QRCodeImgFileName="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket="+field[i].Ticket;
                var newTr = resultList.insertRow();                     
                var newTd0 = newTr.insertCell(0);       var newTd1 = newTr.insertCell(1);   var newTd2 = newTr.insertCell(2);
                var newTd3 = newTr.insertCell(3);       var newTd4 = newTr.insertCell(4);   var newTd5 = newTr.insertCell(5);
                var newTd6 = newTr.insertCell(6);
                newTd0.innerHTML = '<div class="showList">'+SceneID+'</div>';
                newTd1.innerHTML ='<div class="showList">'+SceneName+'</div>';
                newTd2.innerHTML ='<div class="showList">'+SceneDesp+'</div>';
                newTd3.innerHTML ='<img src="'+SceneImg+'"style="height:10vw;width:10vw;margin:10px auto;" />';
                newTd4.innerHTML ='<div class="showList">'+SceneUrl+'</div>';
                newTd5.innerHTML ='<img src="'+QRCodeImgFileName+'"style="height:10vw;width:10vw;margin:10px auto;" />';
//              <input type="button" name="download" id="download" class="btn btn-warning downloadBtn" value="下载" />
                newTd6.innerHTML = '<div class="showList"><input type="button" name="delete" id="delete" class="btn btn-danger deleteBtn" value="删除" /><input type="button" name="edit" id="edit" class="btn btn-warning editBtn" value="编辑" /></div>';
            }
            //对于删除按钮有一个绑定动作
            $(".deleteBtn").bind("click",function(){
            	$('#checkDeleteQRCode').modal('show');
            	codeIndex = $(".deleteBtn").index(this);
            	codeImg = field[codeIndex].QRCodeImgFileName;
            	codeTicket = field[codeIndex].Ticket;
            });
            //对于编辑按钮的绑定
            $(".editBtn").bind("click",function(){
            	var index = $(".editBtn").index(this);
            	//跳出一个输入模态框吧 editSceneInfo
            	$('#editSceneInfo .modal-body #unitName').val(field[index].SceneName);
            	$('#editSceneInfo .modal-body #desp').val(field[index].SceneDescription);
            	$('#editSceneInfo .modal-body #sceneImg').attr("src",field[index].SceneImg);
            	$('#editSceneInfo .modal-body #sceneUrl').val(field[index].SceneUrl);
            	$('#editSceneInfo').modal('show');
            	codeTicket = field[index].Ticket;
            });
        }
    }
    function getInfos(){
    	$.ajax({
    		type:"post",
    		url:"QR_Mid.php",
    		dataType:"json",
    		data:{type:0,UserWebID:userId},
    		success:function(data){
    			console.log(eval(data));
    			listshow(data);
    		},
    		error:function(){
    			alertShow("获取数据失败！");
    		}
    	});
    }
    // 保存信息
    function sendInfos(){
    	var unitName = $('#unitName').val();
    	var desp = $('#desp').val();
    	var sceneImg = $('#sceneImg').val();
    	var sceneUrl = $('#sceneUrl').val();
    	// var tel = $('#tel').val();
    	if(unitName == "" || sceneImg == "" || sceneUrl == ""){
    		alertShow("输入信息不完全，请检查必填部分");
    	}
    	else{
    	$.ajax({
    		type:"post",
    		url:"QR_Mid.php",
    		data:{type:1,UserWebID:userId,unitName:unitName,
    			  desp:desp,sceneImg:sceneImg,sceneUrl:sceneUrl},
    		success:function(data){
    		}	  
    	});
    	}
    }
    // 生成二维码
    function createQRCodes(){
    	$("#checkQRInfo").modal('hide');
    	var account = $("#Account").val();
    	var appId = $("#AppId").val();
    	var appS = $("#AppSecret").val();
		var unitName = $('#unitName').val();
    	var desp = $('#desp').val();
//  	var sceneImg = $('#sceneImg').val();
    	var sceneUrl = $('#sceneUrl').val();
    	console.log(sceneImg + " " + sceneUrl);
    	if(unitName == "" || sceneImg == "" || sceneUrl == ""){
    		alertShow("输入信息不完全，请检查必填部分");
    	}
    	else{
    	$.ajax({
    		type:"post",
    		url:"QR_Mid.php",
    		data:{type:2,UserWebID:userId,unitName:unitName,
    			  desp:desp,sceneImg:sceneImg,sceneUrl:sceneUrl,account:account,appId:appId,appS:appS},
    		success:function(data){
    			alertShow(data);
				getInfos();
    		}
    	});
    	}
    }
    function deleteQRCode(index,img,ticket){
    	$('#checkDeleteQRCode').modal('hide');
    	$.ajax({
    		type:"post",
    		url:"QR_Mid.php",
    		data:{type:3,img:img,ticket:ticket},
    		success:function(data){
    			//删除表格行
    			var i = index + 1;
    			$("#resultList tr").eq(i).remove();
    			alertShow(data);
    		}
    	});
    }
    //确认输入信息
    function checkInfo(){
    	getFileAndUpload();
//  	if(sceneImg != null || sceneImg != ""){
//  	var unitName = $('#unitName').val();
//  	var desp = $('#desp').val();
////  	var sceneImg = $('#sceneImg').val();
//  	var sceneUrl = $('#sceneUrl').val();
//  	var l = "<p>标题："+unitName+"</p><p>简介："+desp+"</p><p>场景图片：<img src="+SceneImg+"style='height:15vw;width:15vw;margin:10px auto;' /></p><p>图文链接："+sceneUrl+"</p>";
//  	$("#nowQRInfo").html(l);
//  	$("#checkQRInfo").modal('show');
//  	}
//  	else{
//  		alertShow("所选图片出现问题");
//  	}
    }
    //修改二维码信息
    function editQRCodeInfo(){
    	var unitName = $('#editSceneInfo .modal-body #unitName').val();
        var desp = $('#editSceneInfo .modal-body #desp').val();
        var sceneImg = $('#editSceneInfo .modal-body #sceneImg').val();
        var sceneUrl = $('#editSceneInfo .modal-body #sceneUrl').val();
            	$.ajax({
            		type:"post",
            		url:"QR_Mid.php",
            		data:{ticket:ticket,type:4},
           			success:function(){
           				
           			}
            	});
    }
//-----------------------系统设置------------------------
	//得到该用户的appId和app Secret，如果用户未绑定公众号则在使用其他功能时会有警告
	function getApp(){
		var r = 0;
		$.ajax({
			type:"post",
			url:"changeApp.php",
			dataType:"json",
			data:{user: userId,op:1},
			success:function(data){
				var result = eval(data);
				if(result != null){
					account = result[0].WeChatAccount;
					id = result[0].AppId;
					secret = result[0].AppSecret;
					$("#change").eq(0).val("确认");
					$("#Account").val(result[0].WeChatAccount);
					$("#AppId").val(result[0].AppId);
					$("#AppSecret").val(result[0].AppSecret);
						$("#AppId").attr("disabled",true);
						$("#AppSecret").attr("disabled",true);
					r = 1;
					if($("#Account").val() == ""){
						alertShow("您尚未绑定公众号，若要实现这些功能需前往系统设置绑定公众号");
					}
					else{
					}
				}			
			}
		});
		res = r;
	}
//---------------------------系统设置----------------------------
	//不能单独修改app，除非账号修改时
	function whileAccountChange(){
		//监听账号输入框
		$("#Account").bind('input propertychange', function(){ 
			$("#AppId").attr("disabled",false);
			$("#AppSecret").attr("disabled",false);
		});
	}
//	修改公众号信息
	function changeApp(){
		$("#change").click(function(){
			$('#checkChangeApp').modal('show');
		});
		$("#reset").eq(0).click(function(){
			getApp();
			$('#Account').val(account);
			$("#AppId").val(id);
			$("#AppSecret").val(secret);
		});
	}
	function realChangeApp(){
			var account = $("#Account").val();
			var appid = $("#AppId").val();
			var apps = $("#AppSecret").val();
			if(account != "" && appid != "" && apps !=""){
			//对app进行验证，验证其有效性0
			$.ajax({
				type:"post",
				url:"changeApp.php",
				data:{op:3,account:account, appid: appid, apps: apps},
				dataType:"text",
				success:function(data){
					if(data != "0"){
						//app有效
						console.log(data);
						updateApp(account, appid, apps);
					}
					else{
						$('#checkChangeApp').modal('hide');
						alertShow("appID或appSecret错误，请重新确认后修改!");
					}
				}
			});
			}
			else{
				updateApp(account, appid, apps);
			}
	}
	function updateApp(account, appid, apps){
		$('#checkChangeApp').modal('hide');
			$.ajax({
				type:"post",
				url:"changeApp.php",
				dataType:"text",
				data:{op:2,user: userId, account:account, appid: appid, apps: apps},
				success: function(data){
					alertShow("修改成功");
						$("#AppId").attr("disabled",true);
						$("#AppSecret").attr("disabled",true);
					getStatisticData();
					getUserInfor();
					getInfos();
				}
			});
	}
//	修改密码
	function changePwd(){
		$("#changepwd").eq(0).click(function(){
			//先确认新密码是否相同
			var newp = $("#newSecret").val();
			var cnewp = $("#checkNewSecret").val();
			var op = $("#oldSecret").val();
			if(newp != cnewp || newp == "" || cnewp == ""){
				alertShow("两次输入新密码不相同,请重新输入");
			}
			else{
				//去获得当前的密码
				var p = 1;
				$.ajax({
					type:"post",
					url:"changePwd.php",
					dataType:"text",
					data:{user:userId,op:1},
					success:function(data){
						p = data;
				if(op == p){
					//继续修改密码
					$.ajax({
						type:"post",
						url:"changePwd.php",
						dataType:"text",
						data:{op:2,user: userId, pwd:newp},
						success: function(data){
							if(data == "1"){
								alertShow("密码修改成功");
							}
							else{
								alertShow("密码修改失败");
							}
						}
					});
				}
				else{
					//重新输入原密码
					alertShow("原密码不正确，请重新输入密码");
				}
					}
				});
			}
		});
		$("#reset").eq(1).click(function(){
			$('#oldSecret').val("");
			$('#newSecret').val("");
			$('#checkNewSecret').val("");
		});
	}
	//刷新清除session
	function removeSession(){
		$.ajax({
			type:"post",
			url:"unsetSession.php",
			success:function(data){
			}
		});
	}
	function signOut(){
		//清除session并返回登录页面
		$.ajax({
			type:"post",
			url:"unsetSession.php",
			success:function(){
				window.location='signin.html';
			}
		});
	}
	//关闭功能页
	function closeContentPage(){
//		$('.closeBtn').click(function(){
			var i = $('.closeBtn').index(this);
			init();
			$(".contentPage").eq(i).css('transform', 'scale(0.0)');
			$(".settingChange").eq(0).show();
			getInfomation(i);
//		});
	}
</script>
</html>