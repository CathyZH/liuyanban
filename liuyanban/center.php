<?php
/**
* TestGuest Version1.0
* ================================================
* Copy 2010-2012 yc60
* Web: http://www.yc60.com
* ================================================
* Author: Lee
* Date: 2010-9-2
*/

//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'center');

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//是否正常登录
if(_is_login()) {
	//获取数据
	$_sql = "SELECT us_name,us_sex,us_face,us_email,us_level,us_reg_time,us_login_time FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	if ($_row) {
		$_data= array();
		$_data['username'] = $_row['us_name'];
		$_data['sex'] = $_row['us_sex'];
		$_data['face'] = $_row['us_face'];
		$_data['email'] = $_row['us_email'];
		$_data['reg_time'] = $_row['us_reg_time'];
		$_data['login_time'] = $_row['us_login_time'];
		switch ($_row['us_level']) {
			case 0:
				$_data['level'] = '普通会员';
				break;
			case 1:
				$_data['level'] = '管理员';
				break;
			default:
				$_data['level'] = '出错';
		}
		$_data = _html_chars($_data);
		mysql_free_result($_result);
		mysql_close();
	} else {
		mysql_close();
		_alert_back('此用户不存在！');
	}
} else {
	mysql_close();
	_alert_back('非法登录！');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cathy的简易论坛系统</title>
<?php 
	include ROOT_PATH.'/import.inc.php';
?>
</head>
<body>
<?php 
	require ROOT_PATH.'/header.inc.php';
?>

<div id="center">
<?php 
	require ROOT_PATH.'/center.inc.php';
?>
	<div id="center_main">
		<h2>会员管理中心</h2>
		<dl>
			<dd>用 户 名：<?php echo $_data['username']?></dd>
			<dd>性　　别：<?php echo $_data['sex']?></dd>
			<dd>头　　像：<?php echo $_data['face']?></dd>
			<dd>电子邮件：<?php echo $_data['email']?></dd>
			<dd>注册时间：<?php echo $_data['reg_time']?></dd>
			<dd>登录时间：<?php echo $_data['login_time']?></dd>
			<dd>身　　份：<?php echo $_data['level']?></dd>
		</dl>
	</div>
</div>

<?php 
	require ROOT_PATH.'/footer.inc.php';
?>
</body>
</html>
