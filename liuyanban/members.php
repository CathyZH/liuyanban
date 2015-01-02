<?php
/**
* Author: Cathy
* Date: 2014-10-2
*/
//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'members');

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';


_init_page("SELECT us_id FROM user WHERE us_active=''", 15);

$_sql = "SELECT us_id,us_name,us_sex,us_face,us_email FROM user WHERE us_active='' ORDER BY us_reg_time DESC LIMIT {$GLOBALS['start_row']},{$GLOBALS['page_size']}";

$_result = mysql_query($_sql);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cathy的简易论坛系统</title>
<?php 
	include ROOT_PATH.'/import.inc.php';
?>
<script type="text/javascript" src="js/members.js"></script>
</head>
<body>

<?php 
	require ROOT_PATH.'/header.inc.php';
?>

<div id="members">
	<h2>网友列表</h2>
	<?php 
		$_data = array();
		while (!!$_row = mysql_fetch_array($_result, MYSQL_ASSOC)) {
			$_data['id'] = $_row['us_id'];
			$_data['username'] = $_row['us_name'];
			$_data['face'] = $_row['us_face'];
			$_data['sex'] = $_row['us_sex'];
			$_data['email'] = $_row['us_email'];
			$_data = _html_chars($_data);
	?>
	<dl>
		<dd class="user"><?php echo $_data['username']?>(<?php echo $_data['sex']?>)</dd>
		<dt><img src="<?php echo $_data['face']?>" alt="<?php echo $_data['username']?>" /></dt>
		<dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_data['id']?>">加为好友</a></dd>
		<dd class="message"><a href="javascript:;" name="message" title="<?php echo $_data['id']?>">私人留言</a></dd>
		<dd class="email">邮件：<a href="mailto:<?php echo $_data['email']?>"><?php echo $_data['email']?></a></dd>
	</dl>
	<?php 
		}
		mysql_free_result($_result);
		//_pageing函数调用分页，1|2，1表示数字分页，2表示文本分页
		_show_page(1, null);
	?>
</div>

<?php 
	require ROOT_PATH.'/footer.inc.php';
?>
</body>
</html>