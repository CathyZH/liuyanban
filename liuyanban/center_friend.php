<?php
/**
* Author: Cathy
*/

//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'center_friend');

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//判断是否登录了
if (!_is_login()) {
	_alert_back('请先登录！');
}

if ($_GET['action'] == 'check' && isset($_GET['id'])) { //验证好友
	//危险操作，为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	$_sql = "SELECT us_uniqid FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {				
		mysql_free_result($_result);
		$_data = array();

		$_sql = "UPDATE friend SET fr_state='1' WHERE fr_id='{$_GET['id']}'";
		//修改表里state，从而通过验证
		mysql_query($_sql);
		if (mysql_affected_rows() == 1) {
			mysql_close();
			_location('好友验证成功','center_friend.php');
		} else {
			mysql_close();
			_alert_back('好友验证失败');
		}
	} else {
		mysql_close();
		_alert_back('账号异常，建议重新登录！');
	}
} else if ($_GET['action'] == 'delete' && isset($_POST['ids'])) { //批删除好友
	$_data = array();
	$_data['ids'] = implode(',',$_POST['ids']);
	
	//危险操作，为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	$_sql = "SELECT us_uniqid FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {				
		mysql_free_result($_result);
		
		$_sql = "DELETE FROM friend WHERE fr_id IN ({$_data['ids']})";
		//修改表里state，从而通过验证
		mysql_query($_sql);
		if (mysql_affected_rows()) {
			mysql_close();
			_location('好友删除成功','center_friend.php');
		} else {
			mysql_close();
			_alert_back('好友删除失败');
		}
	} else {
		mysql_close();
		_alert_back('账号异常，建议重新登录！');
	}
} else { //查询数据
	$_sql = "SELECT fr_id FROM friend WHERE friend_name='{$_COOKIE['username']}' OR my_name='{$_COOKIE['username']}'";
	_init_page($_sql ,15);   //初始化分页
	
	$_sql = "SELECT fr_id,fr_state,friend_name,my_name,fr_content,fr_date FROM friend 
		WHERE friend_name='{$_COOKIE['username']}' OR my_name='{$_COOKIE['username']}'
		ORDER BY fr_date DESC LIMIT {$GLOBALS['start_row']},{$GLOBALS['page_size']}";
	$_result = mysql_query($_sql);
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
<script type="text/javascript" src="js/center_friend.js"></script>
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
		<h2>好友设置中心</h2>
		<form method="post" action="?action=delete">
		<table cellspacing="1">
			<tr><th>好友</th><th>请求内容</th><th>时间</th><th>状态</th><th>删除</th></tr>
			<?php 
				$_data = array();
				while (!!$_row = mysql_fetch_array($_result, MYSQL_ASSOC)) {
					$_data['id'] = $_row['fr_id'];
					$_data['friend_name'] = $_row['friend_name'];
					$_data['my_name'] = $_row['my_name'];
					$_data['content'] = $_row['fr_content'];
					$_data['state'] = $_row['fr_state'];
					$_data['date'] = $_row['fr_date'];
					$_data = _html_chars($_data);
					if ($_data['friend_name'] == $_COOKIE['username']) {
						$_data['friend'] = $_data['my_name'];
						if (empty($_data['state'])) {
							$_data['state_html'] = '<a href="?action=check&id='.$_data['id'].'" style="color:red;">等待您的验证</a>';
						} else {
							$_data['state_html'] = '<span style="color:green;">通过验证</span>';
						}
					} else if ($_data['my_name'] == $_COOKIE['username']) {
						$_data['friend'] = $_data['friend_name'];
						if (empty($_data['state'])) {
							$_data['state_html'] = '<span style="color:blue;">对方暂未验证</a>';
						} else {
							$_data['state_html'] = '<span style="color:green;">通过验证</span>';
						}
					}
					$_res = mysql_query("SELECT us_id FROM user WHERE us_name='{$_data['friend_name']}' LIMIT 1");
					$_user_row = mysql_fetch_array($_res);
					$_data['us_id'] = $_user_row['us_id'];
					mysql_free_result($_res);
			?>
			<tr>
				<td><a href="javascript:;" name="message" title="<?php echo $_data['us_id']?>"><?php echo $_data['friend']?></a></td>
				<td title="<?php echo $_data['content']?>"><?php echo _get_title($_data['content'],14)?></td>
				<td><?php echo $_data['date']?></td>
				<td><?php echo $_data['state_html']?></td>
				<td><input type="checkbox" name="ids[]" value="<?php echo $_data['id']?>" /></td>
			</tr>
			<?php 
				}
				mysql_free_result($_result);
			?>
			<tr><td colspan="5"><label for="all">全选 <input type="checkbox" name="chkall" id="all" /></label> <input type="submit" value="批删除" /></td></tr>
		</table>
		</form>
		<?php _show_page(2, null);?>
	</div>
</div>

<?php 
	require ROOT_PATH.'/footer.inc.php';
?>
</body>
</html>