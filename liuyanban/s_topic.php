<?php
/**
* Testmessage Version1.0
* ================================================
* Copy 2010-2012 yc60
* Web: http://www.yc60.com
* ================================================
* Author: Cheng
* Date: 2014-10-2
*/
//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//定义个常量，用来指定脚本页面的名称
define('SCRIPT','s_topic');

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//判断是否登录了
if(!_is_login()) {
	_location('请先登录!', 'login.php');
}


if(isset($_GET['id'])) { //功能一:获取给定id的主题数据，并显示该主题的相关信息
	$_sql = "SELECT us_uniqid,last_read_time FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {
		$_data = array(); //此处数组会在页面中显示
		$_data['last_read_time'] = $_row['last_read_time'];
		mysql_free_result($_result);
		
		//选出给定id的主题
		$_sql = "SELECT * FROM topic WHERE top_id={$_GET['id']} AND reply_for=0";
		$_result = mysql_query($_sql);
		$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
		if($_row) {
			//对于同一个主题，同一个用户间隔半个小时后才能对不是自己创建的主题的浏览量产生影响（使浏览量加一），防止不停刷新对浏览量的影响
			if(!_is_intime(_time_now(), $_data['last_read_time'], 1800)) {
				$_sql = "UPDATE topic SET top_readcount=top_readcount+1 WHERE top_id='{$_GET['id']}' AND us_name!='{$_COOKIE['username']}'";
				mysql_query($_sql);
				//更新该用户在规定时间戳之外的最近一次浏览主题的时间戳
				$_sql = "UPDATE user SET last_read_time=NOW() WHERE us_name='{$_COOKIE['username']}'";
				mysql_query($_sql);
			}
			
			//获取主题的数据
			$_data['top_id'] = $_row['top_id'];
			$_data['top_username'] = $_row['us_name'];
			$_data['title'] = $_row['top_title'];
			$_data['type'] = $_row['top_type'];
			$_data['content'] = $_row['top_content'];
			$_data['readcount'] = $_row['top_readcount'];
			$_data['commendcount'] = $_row['top_replycount'];
			$_data['nice'] = $_row['top_nice'];
			$_data['top_last_access_date'] = $_row['top_last_access_date'];
			$_data['top_create_date'] = $_row['top_create_date'];
			$_data['top_last_modify_date'] = $_row['top_last_modify_date'];
			$_data['reply_for'] = $_data['top_id']; //为回复该主题做准备
			mysql_free_result($_result);
	
			//拿出用户名，去查找用户信息
			$_sql = "SELECT us_id,us_sex,us_face,us_email FROM user WHERE us_name='{$_data['top_username']}' LIMIT 1";
			$_result = mysql_query($_sql);
			$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
			if ($_row) {
				//提取用户信息
				$_data['us_id'] = $_row['us_id'];
				$_data['sex'] = $_row['us_sex'];
				$_data['face'] = $_row['us_face'];
				$_data['email'] = $_row['us_email'];
				$_data = _html_chars($_data);
				mysql_free_result($_result);
			} else {
				mysql_free_result($_result);
				//这个用户已被删除
			}
			
			//读取主题最后修改信息
			if ($_data['top_last_modify_date'] != '0000-00-00 00:00:00') {
				$_data['modify'] = '本贴已由['.$_data['top_username'].']于'.$_data['top_last_modify_date'].'修改过！';
			}
			
			//读取主题的所有留言信息
			$_sql = "SELECT top_id FROM topic WHERE reply_for='{$_data['reply_for']}'";
			_init_page($_sql, 10); //分页
			$_result = mysql_query("SELECT us_name,top_type,top_title,top_content,top_create_date FROM topic WHERE 
				reply_for='{$_data['reply_for']}' ORDER BY top_create_date ASC LIMIT {$GLOBALS['start_row']},{$GLOBALS['page_size']}");
			
			//链接到修改主题：创建主题的人或者管理员可以修改主题
			if ($_data['top_username'] == $_COOKIE['username'] || isset($_SESSION['admin'])) {
				$_data['top_modify'] = '[<a href="m_topic.php?id='.$_data['top_id'].'">修改</a>]';
			}
			
		} else {
			mysql_close();
			_alert_back('不存在这个主题！');
		}
	} else {
		mysql_free_result($_result);
		mysql_close();
		_alert_back('账号异常，建议重新登录！');
	}
} else if(isset($_GET['action']) && $_GET['action'] == 'reply') { //功能二：对主题的回复功能
	//验证码判断
	//_check_code($_POST['code'],$_SESSION['code']); 
	
	$_sql = "SELECT us_uniqid,last_reply_time FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {
		$_data = array(); //此处数组不会再页面中显示
		//获得上一次回复的时间戳，防止频发回复
		$_data['last_reply_time'] = $_row['last_reply_time'];
		mysql_free_result($_result);
		
		//判断用户是否在规定的时间戳之内留言，这样做的目的是防止用户过于频繁的操作
		if(_is_intime(_time_now(), $_data['last_reply_time'], 60)) {
			_alert_back('您的操作过于频繁，请60秒后继续！');
		}
		
		//获取提交过来的数据
		$_data['reply_for'] = $_POST['reply_for'];
		$_data['type'] = $_POST['type'];
		$_data['title'] = _check_topic_title($_POST['title'], 2, 40);
		$_data['content'] = _check_topic_content($_POST['content'], 10);
		$_data['username'] = $_COOKIE['username'];
		
		$_sql = "INSERT INTO topic(top_type,top_title,top_content,us_name,top_create_date,top_last_access_date,reply_for) "
				."VALUES({$_data['type']},'{$_data['title']}','{$_data['content']}','{$_data['username']}',NOW(),NOW(),{$_data['reply_for']})";
		mysql_query($_sql);
		if(mysql_affected_rows() == 1) {
			$_data['time'] = _date();
			//更新用户最近一次的留言时间戳
			mysql_query("UPDATE user SET last_reply_time='{$_data['time']}' WHERE us_name='{$_COOKIE['username']}'");
			//累积主题的留言数量和主题最近一次被访问的时间
			mysql_query("UPDATE topic SET top_replycount=top_replycount+1,top_last_access_date='{$_data['time']}' WHERE reply_for=0 AND top_id='{$_data['reply_for']}'");
			mysql_close();
			_location('留言成功！','s_topic.php?id='.$_data['reply_for']);
		} else {
			mysql_close();
			_alert_back('留言失败！');
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
	require ROOT_PATH.'/import.inc.php';
?>
<script type="text/javascript" src="js/ubb.js"></script>
<script type="text/javascript" src="js/s_topic.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'/header.inc.php';
?>

<div id="topic">
	<h2>主题详情</h2>
	<?php 
	if (!empty($_data['nice'])) { //精华帖
	?>
	<img src="img/nice.gif" alt="精华帖" class="nice" />
	
	<?php 
	}
	//浏览量达到400，并且评论量达到20即可为热帖
	if ($_data['readcount'] >= 400 && $_data['replycount'] >=20) {
	?>
	<img src="img/hot.gif" alt="热帖" class="hot" />
	
	<?php 
	}
	if ($GLOBALS['curr_page'] == 1) {
	?>
	<div id="subject">
		<dl>
			<dd class="user"><?php echo $_data['top_username']?>(<?php echo $_data['sex']?>)[楼主]</dd>
			<dt><img src="<?php echo $_data['face']?>" alt="<?php echo $_data['top_username']?>" /></dt>
			<dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_data['us_id']?>">加为好友</a></dd>
			<dd class="message"><a href="###">私人留言</a></dd>
			<dd class="email">邮件：<a href="mailto:<?php echo $_data['email']?>"><?php echo $_data['email']?></a></dd>
		</dl>
		<div class="content"> 
 			<div class="user">
				<span>
					1#
				</span><?php echo $_data['top_username']?> | 发表于：<?php echo $_data['top_create_date']?> | 最近访问时间：<?php echo $_data['top_last_access_date']?>
 			</div>
			<h3>主题：<?php echo $_data['title']?> <img src="img/icon<?php echo $_data['type']?>.gif" alt="icon" /> </h3>
 			<div class="detail">
				<?php echo _ubb($_data['content'])?>
				<?php //echo $_data['autograph_html']?>
			</div> 
			<!-- <p><?php echo $_data['modify']; ?></p> -->
 			<div class="read"> 
				<!--<p><?php echo $_data['last_modify_date_string']?></p>-->
				浏览：(<?php echo $_data['readcount']?>)  &nbsp;留言：(<?php echo $_data['commendcount']?>)
 			</div> 
		</div>
	</div>	
	<?php }?>
	
 	<p class="line"></p>
 	 
 	<?php 
		$_floor = 2; //标记楼层数
		while (!!$_row = mysql_fetch_array($_result)) {
			$_data['reply_username'] = _html_chars($_row['us_name']);
			$_data['type'] = _html_chars($_row['top_type']);
			$_data['reply_title'] = _html_chars($_row['top_title']);
			$_data['reply_content'] = _html_chars($_row['top_content']);
			$_data['reply_firsttime'] = _html_chars($_row['top_create_date']);
	
			$_resource = mysql_query("SELECT us_id,us_sex,us_face,us_email FROM user WHERE us_name='{$_data['reply_username']}' LIMIT 1");
			if (!!$_row = mysql_fetch_array($_resource, MYSQL_ASSOC)) {
				//提取留言用户的信息
				$_data['us_id'] = $_row['us_id'];
				$_data['sex'] = $_row['us_sex'];
				$_data['face'] = _html_chars($_row['us_face']);
				$_data['email'] = _html_chars($_row['us_email']);
				mysql_free_result($_resource);
				
				//楼层
				if ($GLOBALS['curr_page'] == 1 && $_floor == 2) {
					if ($_data['reply_username'] == $_data['top_username']) { //说明该回复是楼主对其他人的留言的回复
						$_data['username_html'] = $_data['top_username'].'(楼主)';
					} else {
						$_data['username_html'] = $_data['reply_username'].'(沙发)';
					}
				} else {
					$_data['username_html'] = $_data['reply_username'];
				}	
			} else {
				//这个用户可能已经被删除了
			}
			
			//跟帖回复
			if ($_COOKIE['username']) {
				$_data['reply'] = '<span>[<a href="#reply" name="re" title="回复'.($_floor + (($GLOBALS['curr_page']-1) * $GLOBALS['page_size'])).'楼的'.$_data['reply_username'].'">回复</a>]</span>';
			}
	?>
 	
 	<div class="reply">
		<dl>
			<dd class="user"><?php echo $_data['username_html']?>(<?php echo $_data['sex']?>)</dd>
			<dt><img src="<?php echo $_data['face']?>" alt="<?php echo $_data['username_html']?>" /></dt>
			<dd class="friend"><a href="javascript:;" name="friend" title="<?php echo $_data['us_id']?>">加为好友</a></dd>
			<dd class="message"><a href="###">私人留言</a></dd>
			<dd class="email">邮件：<a href="mailto:<?php echo $_data['email']?>"><?php echo $_data['email']?></a></dd>
		</dl>
		<div class="content"> 
 			<div class="user">
				<span><?php echo $_floor + (($GLOBALS['curr_page']-1) * $GLOBALS['page_size']);?>#</span>
				<?php echo $_data['username_html']?> | 发表于：<?php echo $_data['reply_firsttime']?>
 			</div>
			<h3><?php echo $_data['reply_title']?> <img src="img/icon<?php echo $_data['type']?>.gif" alt="icon" /> <?php echo $_data['reply']?></h3>
 			<div class="detail">
				<?php echo _ubb($_data['reply_content'])?>
			</div> 
			
		</div>
	</div>
 	<p class="line"></p>
 	<?php 
			$_floor ++;		
		}
		mysql_free_result($_result);
		
		_show_page(1, '&id='.$_data['reply_for'].'');
	?>

	<h2>我要留言</h2>
	<a name="reply"/>
	<form method="post" action="?action=reply">
		<input type="hidden" name="reply_for" value="<?=$_data['reply_for']?>" />
		<input type="hidden" name="type" value="<?=$_data['type']?>" />
		<dl>
			<dd>标&nbsp;	&nbsp; 题：
				<input type="text" name="title" class="text" value="RE2：<?=$_data['title']?>" readonly="readonly" /> (*必填，2-40位)
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
				<!--<img src="code.php" id="code" onclick="javascript:this.src='code.php?tm='+Math.random();" /> -->
				<input type="submit" class="submit" value="发表留言" />
			</dd>
		</dl>
	</form>
</div>
<?php 
	require ROOT_PATH.'/footer.inc.php';
?>
</body>
</html>