<?php
/**
* Author: Cathy
* Date: 2014-11-24
*/

//查看外部文件是否有权限访问本文件
if(!defined('ACCESS')) {
	exit('Failure to access!');
}

/*头部导航显示的内容因用户是否登录而不同，而用户是否登录是根据客户端的cookie来判断的*/
?>
<div id="header">
	<h1><a href="index.php"></a></h1>
	<div class="navbar">
		<div class="home"><a href="index.php">首页</a></div>
		<?php 
			//已经登录过了			
			if(_is_login()) {
				if(mb_strlen($_COOKIE['username'], 'utf-8') > 3) 
					$_shorname = substr($_COOKIE['username'], 0, 3).'..';
				else 
					$_shorname = $_COOKIE['username'];
				echo "\n\t\t";
				echo '<div id="message">'.$GLOBALS['message_html'].'</div>';
				echo "\n\t\t";
				echo '<div class="members"><a href="members.php">网友</a></div>';
				echo "\n\t\t";
				echo '<div id="skin"><span>换肤</span>'."\n\t\t\t"
						.'<ul>'."\n\t\t\t\t"
							.'<li><a href="###">皮肤一</a></li>'."\n\t\t\t\t"
							.'<li><a href="###">皮肤二</a></li>'."\n\t\t\t\t"
							.'<li><a href="###">皮肤三</a></li>'."\n\t\t\t"
						.'</ul>'."\n\t\t"
					 .'</div>'."\n\t\t";
				echo '<div id="me"><span><a href="###" title="'.$_COOKIE['username'].'">'.$_COOKIE['username']."</a></span>\n\t\t\t"
						.'<ul>'."\n\t\t\t\t"
							.'<li><a href="center.php">个人中心</a></li>'."\n\t\t\t\t"
							.'<li><a href="###">系统消息</a></li>'."\n\t\t\t\t"
							.'<li><a href="###">新手教程</a></li>'."\n\t\t\t\t"
							.'<li><a href="logout.php?action=logout">退出</a></li>'."\n\t\t\t"
						.'</ul>'."\n\t\t"
					 .'</div>';
			} else {
			//未登录，则显示登录注册菜单
				echo '<div class="register"><a href="register.php">注册</a></div>';
				echo "\n\t\t";
				echo '<div class="login"><a href="login.php">登录</a></div>';
				echo "\n\t\t";
				echo '<div class="members"><a href="members.php">网友</a></div>';
				echo "\n\t\t";
				echo '<div id="skin"><span>换肤</span>'."\n\t\t\t"
						.'<ul>'."\n\t\t\t\t"
							.'<li><a href="###">皮肤一</a></li>'."\n\t\t\t\t"
							.'<li><a href="###">皮肤二</a></li>'."\n\t\t\t\t"
							.'<li><a href="###">皮肤三</a></li>'."\n\t\t\t"
						.'</ul>'."\n\t\t"
					 .'</div>'."\n\t\t";
			}
		?>	
	</div>	
</div>
<script type="text/javascript" src="js/nav.js"></script>