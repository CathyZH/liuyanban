<?php
/**
* Author: Cathy
* Date: 2014-11-30
*/
//防止恶意调用
if (!defined('ACCESS')) {
	exit('Failure to access!');
}
?>
	<div id="center_sidebar">
		<h2>中心导航</h2>
		<dl>
			<dt>账号管理</dt>
			<dd><a href="center.php">个人信息</a></dd>
			<dd><a href="m_center.php">修改资料</a></dd>
		</dl>
		<dl>
			<dt>其他管理</dt>
			<dd><a href="center_message.php">私人留言</a></dd>
			<dd><a href="center_friend.php">好友设置</a></dd>
			<dd><a href="###">个人相册</a></dd>
		</dl>
	</div>