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
//写留言
if ($_GET['action'] == 'write') {
	_check_code($_POST['code'],$_SESSION['code']);
	
	$_sql = "SELECT us_uniqid FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);// echo $_COOKIE['uniqid'].'|'.$_row['us_uniqid'];
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {				
		//唯一id号一致后将数据插入数据库
		mysql_free_result($_result);
		
		$_data = array();
		$_data['to_user'] = $_POST['to_user'];
		$_data['from_user'] = $_COOKIE['username'];
		$_data['content'] = _check_content($_POST['content'], 5, 200);
		print_r($_data);
		//写入表
		
		$_sql = "INSERT INTO message(to_user,from_user,me_content,me_date)
			VALUES ('{$_data['to_user']}','{$_data['from_user']}','{$_data['content']}',NOW())";
		mysql_query($_sql);// or die("error".mysql_error());
		//新增成功
		if (mysql_affected_rows() == 1) {
			mysql_close();
			_alert_close('留言成功！');
		} else {
			mysql_close();
			_alert_back('留言失败！');
		}
	} else {
		mysql_free_result($_result);
		mysql_close();
		_alert_close('账号异常，建议重新登录！！');
	}
}else if (isset($_GET['id'])) { //获取数据
	//先根据id查找该用户的信息，并查看我是不是此用户的好友，只有我是ta的好友，才能给ta发私人留言
	$_sql = "SELECT us_name FROM user WHERE us_id='{$_GET['id']}' LIMIT 1";
	$_result = mysql_query($_sql);
	if (!!$_row = mysql_fetch_array($_result, MYSQL_ASSOC)) {
		$_data = array();
		$_data['friend_name'] = $_row['us_name'];
		mysql_free_result($_result);
		
		//查看我在不在此用户的好友列表里，或者对方在不在我的好友列表里
		$_sql = "SELECT fr_state FROM friend WHERE (my_name='{$_data['friend_name']}' AND friend_name='{$_COOKIE['username']}') 
			OR (friend_name='{$_data['friend_name']}' AND my_name='{$_COOKIE['username']}') LIMIT 1";
		$_result = mysql_query($_sql);
		$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
		if($_row) {
			if($_row['fr_state'] == 0) {
				mysql_free_result($_result);
				mysql_close();
				_alert_close('很遗憾，您还未通过['.$_data['friend_name'].']的好友验证，暂时无法留言！');
			}
		} else {
			mysql_free_result($_result);
			mysql_close();
			_alert_close('您还不是['.$_data['friend_name'].']的好友，先加ta为好友吧。');
		}
		
		$_data = _html_chars($_data);
	} else {
		mysql_free_result($_result);
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
<script type="text/javascript" src="js/s_message.js"></script>
</head>
<body>

<div id="friend">
	<h3>私人留言</h3>
	<form method="post" action="?action=write">
	<input type="hidden" name="to_user" value="<?php echo $_data['friend_name']?>" />
	<dl>
		<dd><input type="text" disabled="disabled" value="TO:<?php echo $_data['friend_name']?>" class="text" /></dd>
		<dd><textarea name="content"></textarea></dd>
		<dd><!--验 证 码：<input type="text" name="code" class="text yzm"  />--> <img src="code.php" id="code"/> <input type="submit" class="submit" value="发送留言" /></dd>
	</dl>
	</form>
</div>

</body>
</html>