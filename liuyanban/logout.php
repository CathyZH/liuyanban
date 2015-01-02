<?php
/**
* Author: Cathy
* Date: 2014-11-30
*/
//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//在登录状态下才能退出登录
if(isset($_GET['action']) && $_GET['action'] == 'logout' && _is_login()) {
	$_sql = "SELECT us_uniqid,us_last_ip FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
	$_result = mysql_query($_sql);
	$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
	//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
	if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {
		$_data = array();
		$_data['us_last_ip'] = $_row['us_last_ip'];
		mysql_fetch_array($_result);
		//每次退出时将上次登录的ip地址更新,如果一样的就不更新了
		if($_SERVER['REMOTE_ADDR'] != $_data['us_last_ip']) {
			$_sql = "UPDATE user SET us_last_ip='{$_SERVER['REMOTE_ADDR']}' WHERE us_name='{$_COOKIE['username']}'";
			mysql_query($_sql);
		}
		mysql_close();		
		_unset_login_cookies();
		session_destroy(); //退出时清除所有的会话
		_location('欢迎下次登录！', 'index.php');
	} else {
		mysql_free_result($_result);
		mysql_close();
		_alert_back('账号异常，建议重新登录！');
	}
} else {
	_alert_back('非法操作！');
}

?>