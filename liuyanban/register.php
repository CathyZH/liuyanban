<?php
/**
* Author: Cathy
* Date: 2014-11-25
*/

//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'register');

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//防止用户在登录状态下进行注册
if(_is_login()) {
	_alert_back('您已登录！');
}

//如果表单已提交，则进行验证，并将相应数据提交到数据库中
if(isset($_GET['action']) && $_GET['action'] = 'register') {
	//先判断验证码是否正确
	//_check_code($_POST['code'], $_SESSION['code']);
	
	//创建一个空数组，用来存放提交过来的合法数据
	$_data = Array();
	$_data['uniqid'] = _check_uniqid($_POST['uniqid'], $_SESSION['uniqid']);
	$_data['active'] = _generate_uniqid(); //生成一个唯一id码作为激活码
	$_data['username'] = _check_username($_POST['username'], 2, 20);
	$_data['password'] = _check_password($_POST['password'], $_POST['notpassword'], 6);
	$_data['question'] = _check_question($_POST['question'], 2, 20);
	$_data['answer'] = _check_answer($_POST['answer'], $_POST['question'], 2, 20);
	$_data['sex'] = $_POST['sex'];
	$_data['face'] = $_POST['face'];
	$_data['email'] = _check_email($_POST['email'], 6, 40);
	//print_r($_data);

	//先检查用户名是否已经被注册过
	$_sql = "SELECT us_name FROM user WHERE us_name = '{$_data['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	if(mysql_fetch_array($_result)) {
		_alert_back('对不起，此用户已被注册！');
		mysql_free_result($_result);
	}
	
	//若数据不重复，则插入数据
	$_sql = "INSERT INTO user(us_uniqid, us_active, us_name, us_password, us_question, us_answer, us_sex, us_face, us_email, us_reg_time, us_login_time, us_last_ip)
		VALUES('{$_data['uniqid']}', '{$_data['active']}', '{$_data['username']}', '{$_data['password']}', '{$_data['question']}', '{$_data['answer']}', '{$_data['sex']}',
			'{$_data['face']}','{$_data['email']}', NOW(), NOW(), '{$_SERVER["REMOTE_ADDR"]}')";
	mysql_query($_sql);
	
	//如果成功插入一条数据，并跳转到激活页面
	if(mysql_affected_rows() == 1) {
		//关闭数据库
		mysql_close();
		unset($_SESSION['uniqid']);
		//注册成功后，跳转到账号激活页面,同时将激活码也传递过去
		_location('恭喜您，注册成功！', 'active.php?active='.$_data['active']);
	} else {
		//关闭数据库
		mysql_close();
		unset($_SESSION['uniqid']);
		//注册失败后，重新跳转到注册页面
		_location('很遗憾，注册失败！', 'register.php');
	}	
} else { //表单未提交
	//生成一个唯一的加密的id号
	$_SESSION['uniqid'] = $_uniqid = _generate_uniqid();
}


?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cathy的简易论坛系统--注册</title>
<?php 
	include ROOT_PATH.'/import.inc.php';
?>
<script type="text/javascript" src="js/register.js"></script>
</head>
<body>
<!-- 包含头部文件 -->
<?php include ROOT_PATH.'/header.inc.php';?>

<div id="register">
	<h2>会员注册</h2>
	<form name="register" action="?action=register" method="post">
		<input type="hidden" name="uniqid" value="<?=$_uniqid?>" />
		<dl>
			<dt>请认真填写以下内容</dt>
			<dd>用 户 名：<input type="text" name="username" class="text" > (*必填，2-20位)</dd>
			<dd>密&nbsp; &nbsp; 码：<input type="password" name="password" class="text"/> (*必填，至少六位)</dd>
			<dd>密码确认：<input type="password" name="notpassword" class="text"/> (*必填，同上)</dd>
			<dd>密码提示：<input type="text" name="question" class="text"/> (*必填，2-20位)</dd>
			<dd>问题回答：<input type="text" name="answer" class="text"/> (*必填，2-20位)</dd>
			<dd>性&nbsp; &nbsp; 别：<input type="radio" name="sex" value="男" checked="checked"/>男
				<input type="radio" name="sex" value="女" />女
			</dd>
			<dd class="face">
				<input type="hidden" name="face" value="face/m01.gif" />
				<img id="faceimg" src="face/m01.jpg" alt="头像" title="点击选择头像" />
			</dd>
			<dd>电子邮件：<input type="text" name="email" class="text"/> (*必填，6-40位)</dd>
			<dd>验 证 码：<input type="text" name="code" class="text yzm"/>
			<img id="code" alt="" src="code.php" />
			</dd>
			<dd><input type="submit" class="submit" value="注册"/></dd>
		</dl>	
	</form>
</div>

<!-- 包含尾部文件 -->
<?php include ROOT_PATH.'/footer.inc.php';?>
</body>
</html>