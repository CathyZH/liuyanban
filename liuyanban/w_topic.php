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
define('SCRIPT', 'w_topic');

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//只有登陆过才能新建主题
if (!_is_login()) {
	_location('请先登录！', 'login.php');
}

//通过表单提交
if(isset($_GET['action']) && $_GET['action'] == 'write') {
	//先验证码检查
	//_check_code($_POST['code'], $_SESSION['code']);
	
	$_sql = "SELECT us_uniqid,last_wtopic_time FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);// echo $_COOKIE['uniqid'].'|'.$_row['us_uniqid'];
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {				
		//唯一id号一致后将数据插入数据库
		$_data = array();
		$_data['last_wtopic_time'] = $_row['last_wtopic_time']; //$_data['last_wtopic_time']保存的是上一次该用户新建主题的时间戳		
		mysql_free_result($_result);
		
		//判断用户是否在规定的时间戳之内新建主题，这样做的目的是防止用户过于频繁的新建主题
		if(_is_intime(_time_now(), $_data['last_wtopic_time'], 60)) {
			_alert_back('您的操作过于频繁，请60秒后继续！');
		}
		
		//以下是需要写入数据库中的数据
		$_data['username'] = $_COOKIE['username'];
		$_data['type'] = $_POST['type'];
		$_data['title'] = _check_topic_title($_POST['title'], 2, 40);
		$_data['content'] = _check_topic_content($_POST['content'], 10);
	
		$_sql = "INSERT INTO topic(top_type,top_title,top_content,us_name,top_create_date,top_last_access_date) "
				."VALUES({$_data['type']},'{$_data['title']}','{$_data['content']}','{$_data['username']}',NOW(),NOW())";
		mysql_query($_sql);
		if(mysql_affected_rows() == 1) {
			$_data['id'] = mysql_insert_id(); //获得新插入的那条数据对应的id
			
			$_data['time_now'] = _time_now(); //保存当前的时间戳
			//更新用户表中上一次用户发表主题的时间戳字段，设置为当前的时间戳
			$_sql = "UPDATE user SET last_wtopic_time='{$_data['time_now']}' WHERE us_name='{$_COOKIE['username']}'";
			mysql_query($_sql);	
			
			mysql_close();
			_location('您已成功创建一个主题！', 's_topic.php?id='.$_data['id']);
		} else {
			mysql_close();
			_alert_back('主题新建失败！');
		}					
	} else {
		mysql_free_result($_result);
		mysql_close();
		_alert_back('账号异常，建议重新登录！');
	}		
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

<div id="w_topic">
	<h2>新建主题</h2>
	<form method="post" name="w_topic" action="?action=write">
		<dl>
			<dt>请认真填写一下内容</dt>
			<dd>主题类型：
				<?php 
					foreach(range(1, 16) as $_num) {
						if($_num == 1) {
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
				<input type="text" name="title" class="text" /> (*必填，2-40位)
			</dd>
			<dd id="q">贴&nbsp; &nbsp; 图：
				<a href="javascript:;">Q图系列[1]</a>　 
				<a href="javascript:;">Q图系列[2]</a>　 
				<a href="javascript:;">Q图系列[3]</a>
			</dd>
			<dd>
				<?php include ROOT_PATH.'/ubb.inc.php'?>
				<textarea name="content" rows="9"></textarea>
			</dd>
			<dd><!--验 证 码：<input type="text" name="code" class="text yzm"  />--> 
				<!--<img src="code.php" id="code" onclick="javascript:this.src='code.php?tm='+Math.random();" />-->
				<input type="submit" class="submit" value="新建主题" />
			</dd>
		</dl>
	</form>
</div>

<?php 
	require ROOT_PATH.'/footer.inc.php';
?>
</body>
</html>