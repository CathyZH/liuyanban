<?php
/**
* Author: Cathy
*/

//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'index');

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

/*主题列表*/
_init_page("SELECT top_id FROM topic WHERE reply_for=0", 10);
$_sql = "SELECT top_id,top_title,top_type,top_readcount,top_replycount FROM topic WHERE reply_for=0 ORDER BY top_last_access_date DESC LIMIT {$GLOBALS['start_row']},{$GLOBALS['page_size']}";
$_result = mysql_query($_sql);

/*最新图片*/

/*新进会员显示*/
//获取本地xml文件中的数据
$_data = _html_chars(_get_xml('new.xml'));

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cathy的简易论坛系统</title>
<?php 
	include ROOT_PATH.'/import.inc.php';
?>
<script type="text/javascript" src="js/index.js"></script>
</head>
<body>
<!-- 包含头部文件 -->
<?php include ROOT_PATH.'/header.inc.php'; ?>

<div id="container">
	<div id="list">
		<h2>主题列表</h2>
		<a href="w_topic.php" class="w_topic">新建主题</a>
		<ul class="topic">
		<?php
			$_topic = array();		
			while(!!$_row = mysql_fetch_array($_result, MYSQL_ASSOC)) {
				$_topic['id'] = $_row['top_id'];
				$_topic['type'] = $_row['top_type'];
				$_topic['readcount'] = $_row['top_readcount'];
				$_topic['replycount'] = $_row['top_replycount'];
				$_topic['title'] = $_row['top_title'];
				$_topic = _html_chars($_topic);
				echo '<li class="icon'.$_topic['type'].'"><em>浏览(<strong>'.$_topic['readcount'].'</strong>) '
					.'留言(<strong>'.$_topic['replycount'].'</strong>)</em> '
					.'<a href="s_topic.php?id='.$_topic['id'].'">'._get_title($_topic['title'],20).'</a></li>';
			}
			mysql_free_result($_result);
			mysql_close();
		?>
	</ul>
	<?php _show_page(2, null);?>
	</div>	
	<div id="user">
		<h2>新进会员</h2>
		<dl>
			<dd class="user"><?php echo $_data['username']?>(<?php echo $_data['sex']?>)</dd>
			<dt><img src="<?php echo $_data['face']?>" alt="<?php echo $_data['username']?>" /></dt>
			<dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_data['id']?>">加为好友</a></dd>
			<dd class="message"><a href="javascript:;" name="message" title="<?php echo $_data['id']?>">私人留言</a></dd>
			<dd class="email">邮件：<a href="mailto:<?php echo $_data['email']?>"><?php echo $_data['email']?></a></dd>
		</dl>
	</div>	
	<div id="pics">
		<h2>最新图片</h2>
		<img src="picture/pic1.jpg" />
	</div>
</div>

<!-- 包含尾部文件 -->
<?php include ROOT_PATH.'/footer.inc.php';?>
</body>
</html>