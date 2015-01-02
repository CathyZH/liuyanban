<?php
/**
* Author: Cathy
* Date: 2014-10-5
*/
//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'qexp');

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';

//初始化
if(!isset($_GET['num']) || !isset($_GET['path'])) {
	_alert_back('非法操作');
} 
if(!is_numeric($_GET['num']) || !is_dir(substr(ROOT_PATH, 0, -7).$_GET['path'])) {
	_alert_back('Error 404: 找不到指定的文件！');
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>多用户留言系统--Q图选择</title>
<?php 
	require ROOT_PATH.'/import.inc.php';
?>
<script type="text/javascript" src="js/qopener.js"></script>
</head>
<body>

<div id="q">
	<h3>选择Q图</h3>
	<dl>
		<?php foreach (range(1,$_GET['num']) as $_num) {?>
		<dd><img src="<?php echo $_GET['path'].$_num?>.gif" alt="<?php echo $_GET['path'].$_num?>.gif" title="头像<?php echo $_num?>" /></dd>
		<?php }?>
		
	</dl>
</div>

</body>
</html>
