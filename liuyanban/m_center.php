<?php
/**
* Author: Cathy
*/

//定义常量ACCESS，用来对本页面授权访问include文件夹下的文件
define('ACCESS',true);

//定义一个常量来指定本页的脚本名称
define('SCRIPT', 'm_center');

//定义常量IS_CONN，用来确定是否连接数据库
define('IS_CONN', true);

//引入公共的包含文件
require dirname(__FILE__).'/include/common.inc.php';


if (_is_login()) {
	//修改资料
	if ($_GET['action'] == 'modify') {
		//_check_code($_POST['code'],$_SESSION['code']);
		
		$_sql = "SELECT us_uniqid FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
		$_result = mysql_query($_sql);
		$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
		//在将数据写入数据库中时先检查客户端的用户的唯一id号是否与数据库中的保持一致，以防cookie的伪造
		if(_is_uniqid_consistent($_COOKIE['uniqid'], $_row['us_uniqid'])) {
			mysql_free_result($_result);
			$_data = array();
			$_data['password'] = $_POST['password'];_check_password($_POST['password'], null, 6);
			$_data['sex'] = $_POST['sex'];
			$_data['face'] = $_POST['face'];
			$_data['email'] = _check_email($_POST['email'], 6, 40);

			//修改资料
			if (empty($_data['password'])) { //密码未更新
				$_sql = "UPDATE user SET us_sex='{$_data['sex']}',us_face='{$_data['face']}',us_email='{$_data['email']}'
					WHERE us_name='{$_COOKIE['username']}'";
				mysql_query($_sql);
			} else { //密码更新
				$_data['password'] = _check_password($_data['password'], null, 6);
				$_sql = "UPDATE user SET us_password='{$_data['password']}',us_sex='{$_data['sex']}',us_face='{$_data['face']}',us_email='{$_data['email']}'
					WHERE us_name='{$_COOKIE['username']}'";
				mysql_query($_sql);
			}
			//判断是否修改成功
			if (mysql_affected_rows() == 1) {
				mysql_close();
				_location('恭喜你，修改成功！','center.php');
			} else {
				mysql_close();
				_location('很遗憾，没有任何数据被修改！','m_center.php');
			}
		} else {
			mysql_close();
			_alert_back('账号异常，建议重新登录！');
		}
	} else { //获取用户数据并显示在页面上
		//获取数据
		$_sql = "SELECT us_name,us_sex,us_face,us_email,us_level,us_reg_time FROM user WHERE us_name='{$_COOKIE['username']}' LIMIT 1";
		$_result = mysql_query($_sql);
		$_row = mysql_fetch_array($_result, MYSQL_ASSOC);
		if ($_row) {
			$_data= array();
			$_data['username'] = $_row['us_name'];
			$_data['sex'] = $_row['us_sex'];
			$_data['face'] = $_row['us_face'];
			$_data['email'] = $_row['us_email'];
			$_data['level'] = $_row['us_level'];
			$_data['reg_time'] = $_row['us_reg_time'];
			switch ($_row['us_level']) {
				case 0:
					$_data['level'] = '普通会员';
					break;
				case 1:
					$_data['level'] = '管理员';
					break;
				default:
					$_data['level'] = '出错';
			}
			$_data = _html_chars($_data);
			
			//性别选择
			if ($_data['sex'] == '男') {
				$_data['sex_html'] = '<input type="radio" name="sex" value="男" checked="checked" /> 男 <input type="radio" name="sex" value="女" /> 女';
			} elseif ($_data['sex'] == '女') {
				$_data['sex_html'] = '<input type="radio" name="sex" value="男" /> 男 <input type="radio" name="sex" value="女" checked="checked" /> 女';
			}
			
			//头像选择
			$_data['face_html'] = '<select name="face">';
			foreach (range(1,9) as $_num) {
				if ($_data['face'] == 'face/m0'.$_num.'.jpg') {
					$_data['face_html'] .= '<option value="face/m0'.$_num.'.jpg" selected="selected">face/m0'.$_num.'.jpg</option>';
				} else {
					$_data['face_html'] .= '<option value="face/m0'.$_num.'.jpg">face/m0'.$_num.'.jpg</option>';
				}
			}
			foreach (range(10,65) as $_num) {
				if ($_data['face'] == 'face/m'.$_num.'.jpg') {
					$_data['face_html'] .= '<option value="face/m'.$_num.'.jpg" selected="selected">face/m'.$_num.'.jpg</option>';
				} else {
					$_data['face_html'] .= '<option value="face/m'.$_num.'.jpg">face/m'.$_num.'.jpg</option>';
				}
			}
			$_data['face_html'] .= '</select>';
		} else {
			_alert_back('此用户不存在！');
		}
	}
} else {
	mysql_close();
	_alert_back('请先登录！');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cathy的简易论坛系统</title>
<?php 
	include ROOT_PATH.'/import.inc.php';
?>
<script type="text/javascript" src="js/m_center.js"></script>
</head>
<body>
<?php 
	require ROOT_PATH.'/header.inc.php';
?>

<div id="center">
<?php 
	require ROOT_PATH.'/center.inc.php';
?>
	<div id="center_main">
		<h2>会员管理中心</h2>
		<form method="post" action="?action=modify">
		<dl>
			<dd>用 户 名：<?php echo $_data['username']?></dd>
			<dd>密　　码：<input type="password" class="text" name="password" /> (留空则不修改)</dd>
			<dd>性　　别：<?php echo $_data['sex_html']?></dd>
			<dd>头　　像：<?php echo $_data['face_html']?></dd>
			<dd>电子邮件：<input type="text" class="text" name="email" value="<?php echo $_data['email']?>" /></dd>
			<dd>注册时间：<?php echo $_data['reg_time']?></dd>
			<dd>身 &nbsp; &nbsp;份：<?php echo $_data['level']?></dd>
			<dd><!--验 证 码：<input type="text" name="code" class="text yzm"  />-->
			<!--<img src="code.php" id="code" />-->
			<input type="submit" class="submit" value="修改资料" /></dd>
		</dl>
		</form>
	</div>
</div>

<?php 
	require ROOT_PATH.'/footer.inc.php';
?>
</body>
</html>
