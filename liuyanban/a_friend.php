<?php
/**
* Author: Cathy
*/

//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'a_friend');

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//判断是否登录了
if (!_is_login()) {
	_alert_close('请先登录！');
}

//添加好友
if ($_GET['action'] == 'add') {
	_check_code($_POST['code'],$_SESSION['code']);
	
	$_sql = "SELECT us_uniqid FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);// echo $_COOKIE['uniqid'].'|'.$_row['us_uniqid'];
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {
		//唯一id号一致后将数据插入数据库
		mysql_free_result($_result);
	
		$_data = array();
		$_data['friend_name'] = $_POST['friend_name'];
		$_data['my_name'] = $_COOKIE['username'];
		$_data['content'] = _check_content($_POST['content'], 10, 200);

		//不能添加自己
		if ($_data['my_name'] == $_data['friend_name']) {
			_alert_close('请不要添加自己！');
		}
		
		//数据库验证好友是否已经添加
		$_sql = "SELECT fr_id,fr_state FROM friend WHERE (friend_name='{$_data['friend_name']}' AND my_name='{$_data['my_name']}') 
			OR (friend_name='{$_data['my_name']}' AND my_name='{$_data['friend_name']}') LIMIT 1";	
		$_result = mysql_query($_sql);
		if (!!$_row = mysql_fetch_array($_result, MYSQL_ASSOC)) {
			$_data['fr_state'] = $_row['fr_state'];
			mysql_free_result($_result);
			mysql_close();
			if($_data['fr_state'])
				_alert_close('你们已经是好友了！无需添加！');
			else 
				_alert_close('暂未通过好友验证，请耐心等待！');
		}
		
		//添加好友信息
		$_sql = "INSERT INTO friend(my_name, friend_name,fr_content,fr_date) 
			VALUES('{$_data['my_name']}','{$_data['friend_name']}','{$_data['content']}',NOW())";
		mysql_query($_sql);
		if(mysql_affected_rows() == 1) {
			mysql_close();
			_alert_close('好友添加成功！请等待验证！');
		} else {
			mysql_close();
			_alert_back('好友添加失败！');
		}
	}
}

//获取数据
if (isset($_GET['id'])) {
	$_sql = "SELECT us_name FROM user WHERE us_id='{$_GET['id']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	if($_row) {
		$_data = array();
		$_data['frind_name'] = $_row['us_name'];
		$_data = _html_chars($_data);
		mysql_free_result($_result);
	} else {
		mysql_close();
		_alert_close('不存在此用户！');
	}
} else {
	mysql_close();
	_alert_close('非法操作！');
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
<script type="text/javascript" src="js/a_friend.js"></script>
</head>
<body>

<div id="friend">
	<h3>添加好友</h3>
	<form method="post" action="?action=add">
	<input type="hidden" name="friend_name" value="<?=$_data['frind_name']?>" />
	<dl>
		<dd><input type="text" disabled="disabled" value="TO:<?=$_data['frind_name']?>" class="text" /></dd>
		<dd><textarea name="content">我非常想和你交朋友！</textarea></dd>
		<dd><!--验 证 码：<input type="text" name="code" class="text yzm"  />--> <!--<img src="code.php" id="code" />--> <input type="submit" class="submit" value="添加好友" /></dd>
	</dl>
	</form>
</div>

</body>
</html>