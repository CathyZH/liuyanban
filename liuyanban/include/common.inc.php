<?php
/**
* Author: Cathy
* Date: 2014-11-24
*/

/*****查看外部文件是否有权限访问本文件*******/
if(!defined('ACCESS')) {
	exit('Failure to access!');
}

//默认开启会话
session_start();

//设置页面编码
header('Content-type:text/html;charset=utf-8');

//定义硬路径常量，以加快文件的访问速度
define('ROOT_PATH', dirname(__FILE__));

//拒绝低版本php
if(PHP_VERSION < '4.1.0') {
	exit('PHP_VERSION is too Low!');
}

//定义一个反映表单字符串是否自动转义的状态常量GPC
define('GPC', get_magic_quotes_gpc());

//包含全局函数库
require ROOT_PATH.'/global.func.php';

//定义包含该文件的脚本开始执行的时间常量
define('START_TIME', _time_now());

//在需要时连接数据库
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PWD', 'localhost');
define('DB_NAME', 'liuyanban');
define('DB_CHARSET', 'UTF8');
if(IS_CONN) {
	//创建连接
	_connect_server(DB_HOST, DB_USER, DB_PWD);	
	//指定数据库
	_select_db(DB_NAME);	
	//设置字符集
	_select_charset(DB_CHARSET);
}

if(_is_login()) {
	//统计待查看的私人留言数量
	$_sql = "SELECT COUNT(me_id) AS num FROM message WHERE to_user='{$_COOKIE['username']}' AND me_state=0 ";
	$_message_result = mysql_query($_sql);
	$_message_row = mysql_fetch_array($_message_result, MYSQL_ASSOC);
	if(empty($_message_row['num'])) {
		$GLOBALS['message_html'] = '<strong class="noread"><a href="center_message.php" title="私人留言">(0)</a></strong>';
	} else {
		$GLOBALS['message_html'] = '<strong class="read"><a href="center_message.php" title="私人留言">('.$_message_row['num'].')</a></strong>';
	}
}
?>