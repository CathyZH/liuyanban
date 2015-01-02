<?php
/**
* Author: Cathy
* Date: 2014-11-28
*/
//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'login');

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

if(isset($_GET['action']) && $_GET['action'] == 'login') {
	//先判断验证码是否正确
	//_check_code($_POST['code'], $_SESSION['code']);
	
	//获得用户名，密码，保留时间信息
	$_username = $_POST['username'];
	$_password = sha1($_POST['password']);
	$_time = $_POST['time'];
	
	//将用户名与密码和数据库进行对比(注意激活码)
	$_sql = "SELECT us_name, us_uniqid,us_active,us_level FROM user WHERE us_name='$_username' AND us_password='$_password' LIMIT 1";
	$_result = mysql_query($_sql);
	if(!!$_row = mysql_fetch_array($_result, MYSQL_ASSOC)) {
		$_data = array();
		$_data['active'] = $_row['us_active'];
		$_data['username'] = $_row['us_name'];
		$_data['uniqid'] = $_row['us_uniqid'];
		$_data['level'] = $_row['us_level'];
		mysql_free_result($_result);
		//如果激活码存在，则跳转到激活页面
		if($_data['active']) {
			_location('账户未被激活！点击跳转到激活页面。', 'active.php?active='.$_data['active']);
			mysql_close();
		} else {
			//将用户名和唯一标识符存入cookie
			_set_login_cookies($_data['username'], $_data['uniqid'], $_time);
			//如果是管理员就将管理员标志存入到session中去
			if($_data['level']) {
				$_SESSION['admin'] = $_data['level'];
			}
			//更新用户的登录时间
			$_data['login_time'] = _date();
			$_sql = "UPDATE user SET us_login_time='{$_data['login_time']}' WHERE us_name='{$_data['username']}'";
			mysql_query($_sql);
			mysql_close();
			_location('亲爱的'.$_data['username'].'，欢迎回来！', 'index.php');
		}				
	} else {
		mysql_close();
		_location('用户名或密码不正确！', 'login.php');
	}	
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cathy的简易论坛系统--登录</title>
<?php 
	include ROOT_PATH.'/import.inc.php';
?>
<script type="text/javascript" src="js/login.js"></script>
</head>
<body>
<!-- 包含头部文件 -->
<?php include ROOT_PATH.'/header.inc.php';?>

<div id="login">
	<h2>会员登录</h2>
	<form name="login" action="?action=login" method="post">
		<dl>
			<dd>用 户 名：<input type="text" name="username" class="text"/></dd>
			<dd>密&nbsp; &nbsp; 码：<input type="password" name="password" class="text"/></dd>
			<dd>保&nbsp; &nbsp; 留：<input type="radio" name="time" value="0" checked="checked"/>不保留
				<input type="radio" name="time" value="1" />一天
				<input type="radio" name="time" value="2" />一周
				<input type="radio" name="time" value="3" />一月
			</dd>
			<dd><!--验 证 码：<input type="text" name="code" class="text code"/>-->
				<!--<img id="code" alt="" src="code.php" />-->
			</dd>
			<dd>
				<input type="submit" class="button" value="登录"/>
				<input type="button" class="button" value="注册"/>
			</dd>
		</dl>	
	</form>
</div>

<!-- 包含尾部文件 -->
<?php include ROOT_PATH.'/footer.inc.php';?>
</body>
</html>