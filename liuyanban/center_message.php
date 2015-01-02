<?php
/**
* Author: Cathy
*/

//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'center_message');

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//判断是否登录了
if (!_is_login()) {
	_alert_back('请先登录！');
}

if ($_GET['action'] == 'delete' && isset($_POST['ids'])) { //批删除留言
	$_data = array();
	$_data['ids'] = implode(',',$_POST['ids']);
	
	//危险操作，为了防止cookies伪造，还要比对一下唯一标识符uniqid()
	$_sql = "SELECT us_uniqid FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {				
		mysql_free_result($_result);
		
// 		$_sql = "SELECT me_state FROM message WHERE me_id IN ({$_data['ids']})";
// 		$_result = mysql_query($_sql);
// 		while(!!$_row = mysql_fetch_array($_result, MYSQL_ASSOC)) 
// 			if($_row['me_state'] == 0) 
// 				_confirm('存在未查阅的留言，确认删除它们吗？');
// 		mysql_free_result($_result);
		//批删除
		$_sql = "DELETE FROM message WHERE me_id IN ({$_data['ids']})";
		mysql_query($_sql);
		if (mysql_affected_rows()) {
			mysql_close();
			_location('删除成功！','center_message.php');
		} else {
			mysql_close();
			_alert_back('删除失败！');
		}
	} else {
		mysql_close();
		_alert_back('账号异常，建议重新登录！');
	}
} else { //查询数据	
	$_sql = "SELECT me_id FROM message WHERE to_user='{$_COOKIE['username']}'";
	_init_page($_sql ,15);   //初始化分页
	
	$_sql = "SELECT me_id,me_state,from_user,to_user,me_content,me_date FROM message
		WHERE to_user='{$_COOKIE['username']}' ORDER BY me_date DESC LIMIT {$GLOBALS['start_row']},{$GLOBALS['page_size']}";
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
<script type="text/javascript" src="js/center_message.js"></script>
</head>
<body>

<!-- 包含头部文件 -->
<?php include ROOT_PATH.'/header.inc.php'; ?>

<div id="center">
<?php 
	require ROOT_PATH.'/center.inc.php';
?>
	<div id="center_main">
		<h2>私人留言管理中心</h2>
		<form method="post" action="?action=delete">
		<table cellspacing="1">
			<tr><th>留言人</th><th>留言内容</th><th>时间</th><th>状态</th><th>操作</th></tr>
			<?php 
				$_data = array();
				while (!!$_row = mysql_fetch_array($_result)) {
					$_data['id'] = $_row['me_id'];
					$_data['from_user'] = $_row['from_user'];
					$_data['content'] = $_row['me_content'];
					$_data['date'] = $_row['me_date'];
					$_data = _html_chars($_data);
					if (empty($_row['me_state'])) {
						$_data['state'] = '<img src="img/read.gif" alt="未读" title="未读" />';	
						$_data['content_html'] = '<strong>'._get_title($_data['content'],14).'</strong>';
					} else {
						$_data['state'] = '<img src="img/noread.gif" alt="已读" title="已读" />';	
						$_data['content_html'] = _get_title($_data['content'],14);
					}
					
			?>
			<tr>
				<td><?php echo $_data['from_user']?></td>
				<td><a href="message_detail.php?id=<?php echo $_data['id']?>" title="<?php echo $_data['content']?>"><?php echo $_data['content_html']?></a></td>
				<td><?php echo $_data['date']?></td><td><?php echo $_data['state']?></td>
				<td><input name="ids[]" value="<?php echo $_data['id']?>" type="checkbox" /></td>
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