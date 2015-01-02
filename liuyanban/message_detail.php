<?php
/**
* Author: Cathy
*/

//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'message_detail');

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//判断是否登录了
if (!_is_login()) {
	_alert_back('请先登录！');
}

//删除
if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
	//这是验证留言是否合法
	$_result = mysql_query("SELECT me_id FROM message WHERE me_id='{$_GET['id']}' LIMIT 1");
	if (!!$_row = mysql_fetch_array($_result)) {
		mysql_free_result($_result);
		
		$_result = mysql_query("SELECT us_uniqid FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1");
		$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
		//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
		if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {				
			mysql_free_result($_result);
			
			//删除
			mysql_query("DELETE FROM message WHERE me_id='{$_GET['id']}' LIMIT 1");
			if (mysql_affected_rows() == 1) {
				mysql_close();
				_location('删除成功！','center_message.php');
			} else {
				mysql_close();
				_alert_back('删除失败');
			}
		} else {
			mysql_close();
			_alert_back('账号异常，建议重新登录！');
		}	
	} else {
		_alert_back('此留言不存在！');
	}
}
//处理id
if (isset($_GET['id'])) {
	$_sql = "SELECT me_state,from_user,me_content,me_date FROM message WHERE me_id='{$_GET['id']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	if ($_row) {
		//将它state状态设置为1即可
		if (empty($_row['me_state'])) {
			$_sql = "UPDATE message SET me_state=1 WHERE me_id='{$_GET['id']}' LIMIT 1";
			mysql_query($_sql);// or die('error'.mysql_error());
			if(mysql_affected_rows() != 1) {
				mysql_free_result($_result);
				mysql_close();
				_alert_back('异常！');
			}
		}
		$_data= array();
		$_data['id']= $_GET['id'];
		$_data['from_user'] = $_row['from_user'];
		$_data['content'] = $_row['me_content'];
		$_data['date'] = $_row['me_date'];
		$_data = _html_chars($_data);
		mysql_free_result($_result);
		
		$_res = mysql_query("SELECT us_id FROM user WHERE us_name='{$_data['from_user']}' LIMIT 1");
		$_user_row = mysql_fetch_array($_res);
		$_data['us_id'] = $_user_row['us_id'];
		mysql_free_result($_res);
	} else {
		mysql_close();
		_alert_back('此留言不存在！');
	}
} else {
	mysql_close();
	_alert_back('非法登录');
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
<script type="text/javascript" src="js/message_detail.js"></script>
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
		<h2>留言详情</h2>
		<dl>
			<dd>留言 人：<?php echo $_data['from_user']?></dd>
			<dd>内　　容：<strong><?php echo $_data['content']?></strong></dd>
			<dd>留言时间：<?php echo $_data['date']?></dd>
			<dd class="button">
				<input type="button"  value="返回列表" id="return" /> 
				<input type="button"  value="回复留言" onclick="javascript:location.href='s_message.php?id=<?= $_data['us_id']?>'" />
				<input type="button" id="delete" name="<?php echo $_data['id']?>" value="删除留言" />
			</dd>
		</dl>
	</div>
</div>		
<?php 
	require ROOT_PATH.'/footer.inc.php';
?>
</body>
</html>