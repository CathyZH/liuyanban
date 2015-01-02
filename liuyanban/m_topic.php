<?php
/**
* Author: Cathy
* Date: 2014-11-30
*/
//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'm_topic');

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//只有登陆过才能新建主题
if (!_is_login()) {
	_location('请先登录！', 'login.php');
}

if(isset($_GET['id'])) { //功能一：从数据库读取给定id的主题
	//选出给定id的主题
	$_sql = "SELECT top_id,top_type,top_title,top_content,us_name FROM topic WHERE top_id={$_GET['id']} AND reply_for=0 LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	if($_row) {
		//获取主题的数据
		$_data = array();
		$_data['top_id'] = $_row['top_id'];
		$_data['title'] = $_row['top_title'];
		$_data['type'] = $_row['top_type'];
		$_data['content'] = $_row['top_content'];
		$_data['top_username'] = $_row['us_name'];
		_html_chars($_data);
		mysql_free_result($_result);	

		//检查是否有权限修改该主题
		if($_data['top_username'] != $_COOKIE['username']) {
			mysql_close();
			_alert_back('对不起，您没有权限修改该主题！');		
		}
	} else {
		mysql_close();
		_alert_back('不存在这个主题！');
	}
} else if(isset($_GET['action']) && $_GET['action'] == 'modify') { //功能二：提交修改后的数据到数据库中
	//先验证码检查
	//_check_code($_POST['code'], $_SESSION['code']);

	$_sql = "SELECT us_uniqid FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {				
		//唯一id号一致	
		mysql_free_result($_result);
		//接受需要更新写入数据库中的数据
		$_data = array();
		$_data['top_id'] = $_POST['top_id'];
		$_data['type'] = $_POST['type'];
		$_data['title'] = _check_topic_title($_POST['title'], 2, 40);
		$_data['content'] = _check_topic_content($_POST['content'], 10);
		//更新数据
		$_sql = "UPDATE topic SET top_type='{$_data['type']}',top_title='{$_data['title']}',top_content='{$_data['content']}' WHERE top_id='{$_data['top_id']}'";
		mysql_query($_sql);
		if(mysql_affected_rows() == 1) {
			//更新主题表中最近一次修改的时间
			$_sql = "UPDATE topic SET top_last_modify_date=NOW() WHERE top_id='{$_data['top_id']}'";
			mysql_query($_sql);
			
			//同时也要更新跟主题相关的留言的类型，标题和修改时间
			$_data['re_title'] = 'RE2：'.$_data['title'];
			$_sql = "UPDATE topic SET top_type='{$_data['type']}',top_title='{$_data['re_title']}',top_last_modify_date=NOW() WHERE reply_for='{$_data['top_id']}'";
			mysql_query($_sql);

			mysql_close();
			_location('主题修改成功！', 's_topic.php?id='.$_data['top_id']);
		} else {
			mysql_close();
			_alert_back('主题修改失败！');
		}					
	} else {
		mysql_free_result($_result);
		mysql_close();
		_alert_back('账号异常，建议重新登录！');
	}
} else {
	_alert_back('非法操作！');
}		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php 
	include ROOT_PATH.'/import.inc.php';
?>
<script type="text/javascript" src="js/ubb.js"></script>
<script type="text/javascript" src="js/topic.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'/header.inc.php';
?>

<div id="m_topic">
	<h2>修改主题</h2>
	<form method="post" name="w_topic" action="?action=modify">
		<input type="hidden" name="top_id" value="<?=$_data['top_id']?>" />
		<dl>
			<dt>请认真修改以下内容</dt>
			<dd>主题类型：
				<?php 
					foreach(range(1, 16) as $_num) {
						if($_num == $_data['type']) {
							echo '<label for="type'.$_num.'">'
									.'<input type="radio" id="type'.$_num.'" name="type" value="'.$_num.'" checked="checked" />';
						} else {
							echo '<label for="type'.$_num.'">'
									.'<input type="radio" id="type'.$_num.'" name="type" value="'.$_num.'" />';		
						}
						echo '<img src="img/icon'.$_num.'.gif" alt="主题类型" />'
								.'</label>&nbsp; ';
						if($_num == 8) {
							echo '<br />&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';		
						}
					}			
				?>		
			</dd>
			<dd>标&nbsp;	&nbsp; 题：
				<input type="text" name="title" class="text" value="<?=$_data['title']?>" /> (*必填，2-40位)
			</dd>
			<dd id="q">贴&nbsp; &nbsp; 图：
				<a href="javascript:;">Q图系列[1]</a>　 
				<a href="javascript:;">Q图系列[2]</a>　 
				<a href="javascript:;">Q图系列[3]</a>
			</dd>
			<dd>
				<?php include ROOT_PATH.'/ubb.inc.php'?>
				<textarea name="content" rows="9"><?=$_data['content']?></textarea>
			</dd>
			<dd><!--验 证 码：<input type="text" name="code" class="text yzm"  />--> 
				<img src="code.php" id="code" onclick="javascript:this.src='code.php?tm='+Math.random();" /> 
				<input type="submit" class="submit" value="修改主题" />
			</dd>
		</dl>
	</form>
</div>

<?php 
	require ROOT_PATH.'/footer.inc.php';
?>
</body>
</html>