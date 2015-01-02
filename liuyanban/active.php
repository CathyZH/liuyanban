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
define('SCRIPT', 'active');

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

if(!isset($_GET['active'])) {
	_alert_back('非法操作！');
}

if(isset($_GET['action']) && $_GET['action'] == 'ok') {
	$_sql = "SELECT us_id,us_name,us_sex,us_face,us_email,us_active FROM user WHERE us_active='{$_GET['active']}' LIMIT 1";
	$_result = mysql_query($_sql);
	if(!!$_row = mysql_fetch_array($_result, MYSQL_ASSOC)) {
		$_sql = "UPDATE user SET us_active=NULL WHERE us_active='{$_GET['active']}'";
		mysql_query($_sql);
		if(mysql_affected_rows() == 1) {
			//账户激活成功后，将新进的会员信息写入xml文件里
			$_data = array();
			$_data['id'] = $_row['us_id'];
			$_data['username'] = $_row['us_name'];
			$_data['sex'] = $_row['us_sex'];
			$_data['face'] = $_row['us_face'];
			$_data['email'] = $_row['us_email'];
			mysql_free_result($_result);
			mysql_close();
			_new_xml('new.xml', $_data);
			_location('账户激活成功！', 'login.php');
		} else {
			mysql_close();
			_location('账户激活失败！', 'register.php');
		}
	} else {
		_alert_back('非法操作！');
	}
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cathy的简易论坛系统--激活</title>
<?php 
	include ROOT_PATH.'/import.inc.php';
?>
</head>
<body>
<!-- 包含头部文件 -->
<?php include ROOT_PATH.'/header.inc.php';?>

<div id="active">
	<h2>激活账户</h2>
	<p>本页面是为了模拟您的邮件系统功能，点击超链接激活您的账户</p>
	<p>
		<a href="active.php?action=ok&active=<?= $_GET['active']?>">
			<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']?>?action=ok&active=<?= $_GET['active']?>
		</a>
	</p>
</div>

<!-- 包含尾部文件 -->
<?php include ROOT_PATH.'/footer.inc.php';?>
</body>
</html>