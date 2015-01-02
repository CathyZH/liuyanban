<?php
/**
* Author: Cathy
* Date: 2014-11-25
*/

/**
 * _time_now() 获取当前的时间戳，单位：秒。精确到毫秒
 * @access public
 * @return number
 */
function _time_now() {
	$_time = explode(' ', microtime());
	return $_time[0] + $_time[1];
}

/**
 * 获得东八区的日期时间
 * @return string
 */
function _date() {
	//设置时区为PRC东八区
	date_default_timezone_set('PRC');
	return  date('Y-m-d H:i:s',time());
}

/**
 * _is_intime 判断是否在规定的时间戳内
 * @param unknown $_now
 * @param unknown $_last_time
 * @param unknown $_in_time
 * @return boolean
 */
function _is_intime($_now, $_last_time, $_in_time) {
	if($_now - $_last_time <= $_in_time)
		return true;
	return false;
}

/**
 * _generate_code()生成验证码图片，并将验证码放到session中
 * @access public
 * @param int $_width
 * @param int $_height
 * @param int $_length
 * @param bool $_flag
 */
function _generate_code($_width=75, $_height=25, $_length=4, $_flag = true) {
	//创建随机码作为验证码
	for($i = 0; $i < $_length; $i++) {
		$_nmsg .= dechex(mt_rand(0, 15));
	}
	//将验证码保存在服务器的会话中--保持持久有效
	$_SESSION['code'] = $_nmsg;
	//创建一张图片
	$_img = imagecreatetruecolor($_width, $_height);
	//创建一个白颜色指派器
	$_white = imagecolorallocate($_img, 255, 255, 255);
	//填充图片颜色
	imagefill($_img, 0, 0, $_white);
	//是否显示边框，默认显示
	if($_flag) {
		//创建一个黑色指派器
		$_black = imagecolorallocate($_img, 0, 0, 0);
		//绘制黑色边框
		imagerectangle($_img, 0, 0, $_width-1, $_height-1, $_black);
	}
	//随机绘制6条线，线条的位置和颜色也是随机分配的
	for($i = 0; $i < 6; $i++) {
		//创建一个随机颜色指派器
		$_rnd_color = imagecolorallocate($_img, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
		//绘制线条
		imageline($_img, mt_rand(0, $_width), mt_rand(0, $_height), mt_rand(0, $_width), mt_rand(0, $_height), $_rnd_color);	
	}
	//随机雪花
	for($i = 0; $i < 100; $i++) {
		//创建一个随机颜色指派器，颜色相对较淡些
		$_rnd_color = imagecolorallocate($_img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
		//在图片上加上水印*
		imagestring($_img, 1, mt_rand(1, $_width), mt_rand(1, $_height), '*', $_rnd_color);
	}
	//输出验证码，且每个验证码的字体大小和颜色都随机分配
	for($i = 0; $i < strlen($_SESSION['code']); $i++) {
		$_rnd_color = imagecolorallocate($_img, mt_rand(0, 100), mt_rand(0, 150), mt_rand(0, 200));
		imagestring($_img, mt_rand(4, 5), $i * $_width / $_length + mt_rand(1, 10),
			mt_rand(1, $_height / 2), $_SESSION['code'][$i], $_rnd_color);
	}
	//设置页面内容的输出类型，这一步相当重要
	header('Content-Type:image/png');
	//输出对应格式的图片
	imagepng($_img);
	//释放图像资源
	imagedestroy($_img);
}

/**
 * _generate_uniqid()生成一个唯一的加密的id号
 * @access public 
 * @return string
 */
function _generate_uniqid() {
	return sha1(uniqid(rand(), true));
}

/**
 * _check_code()验证码是否一致
 * @access public 
 * @param unknown $_code
 * @param unknown $_another_code
 */
function _check_code($_code, $_another_code) {
	if($_code != $_another_code)
		_alert_back('验证码不正确！');
}

/**
 * _check_uniqid()验证唯一标识符
 * @param unknown $_uniqid
 * @param unknown $_another_uniqid
 * @return unknown
 */
function _check_uniqid($_uniqid, $_another_uniqid) {
	if(strlen($_uniqid) != 40 || $_uniqid != $_another_uniqid) {
		_alert_back('唯一标识符异常！');
	}
	return $_uniqid;
}

/**
 * _check_username()验证用户名是否合法
 * @param unknown $_username
 * @param unknown $_min_length
 * @param unknown $_max_length
 * @return Ambigous <unknown, string>
 */
function _check_username($_username, $_min_length, $_max_length) {
	$_username = trim($_username);
	if(mb_strlen($_username, 'utf-8') < $_min_length || mb_strlen($_username, 'utf-8') > $_max_length)
		_alert_back('用户名不得小于'.$_min_length.'位或者不得大于'.$_max_length.'位！');
	//限制敏感字符
	$_pattern = '/[<>\'\"\ \	]/';
	if(preg_match($_pattern, $_username)) {
		_alert_back('用户名不得包含敏感字符！');
	}
	return _escape_string($_username);
}

/**
 * _check_password验证密码是否合法
 * @param unknown $_password
 * @param unknown $_notpassword
 * @param unknown $_min_length
 * @return string
 */
function _check_password($_password, $_notpassword, $_min_length) {
	if(strlen($_password) < $_min_length)
		_alert_back('密码不得小于'.$_min_length.'位！');
	if($_notpassword != null) {
		if(strlen($_password) != strlen($_notpassword))
			_alert_back('密码不一致！');
	}
	//返回加密后的密码
	return sha1($_password);
}

/**
 * _check_question
 * @param unknown $_question
 * @param unknown $_min_length
 * @param unknown $_max_length
 * @return Ambigous <unknown, string>
 */
function _check_question($_question, $_min_length, $_max_length) {
	$_question = trim($_question);
	if(mb_strlen($_question, 'utf-8') < $_min_length || mb_strlen($_question, 'utf-8') > $_max_length)
		_alert_back('密码提示不得小于'.$_min_length.'位或者不得大于'.$_max_length.'位！');
	return _escape_string($_question);
}

/**
 * _check_answer
 * @param unknown $_answer
 * @param unknown $_question
 * @param unknown $_min_length
 * @param unknown $_max_length
 * @return string
 */
function _check_answer($_answer, $_question, $_min_length, $_max_length) {
	$_answer = trim($_answer);
	if(mb_strlen($_answer, 'utf-8') < $_min_length || mb_strlen($_answer, 'utf-8') > $_max_length)
		_alert_back('回答不得小于'.$_min_length.'位或者不得大于'.$_max_length.'位！');
	if($_answer == $_question) 
		_alert_back('密码提示与回答不得相同！');
	return sha1($_answer); //返回加密后的回答	
}

/**
 * _check_email
 * @param unknown $_email
 */
function _check_email($_email, $_min_length, $_max_length) {
	$_email = trim($_email);
	$_pattern = '/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/';
	if(!preg_match($_pattern, $_email)) {
		_alert_back('邮件格式不正确！');
	}
	if(strlen($_email) < $_min_length || strlen($_email) > $_max_length)
		_alert_back('邮箱地址不得小于'.$_min_length.'位或者不得大于'.$_max_length.'位！');
	return _escape_string($_email);
}

/**
 * _check_topic_title 检查主题的标题
 * @param unknown $_title
 * @param unknown $_min_length
 * @param unknown $_max_length
 * @return Ambigous <string, unknown>
 */
function _check_topic_title($_title, $_min_length, $_max_length) {
	$_title = trim($_title);
	if(mb_strlen($_title, 'utf-8') < $_min_length || mb_strlen($_title, 'utf-8') > $_max_length)
		_alert_back('标题不得小于'.$_min_length.'位或者不得大于'.$_max_length.'位！');
	return _escape_string($_title);
}

/**
 * _check_topic_content 检查主题内容
 * @param unknown $_content
 * @param unknown $_min_length
 * @return Ambigous <string, unknown>
 */
function _check_topic_content($_content, $_min_length) {
	$_content = trim($_content);
	if(mb_strlen($_content, 'utf-8') < $_min_length)
		_alert_back('标题不得小于'.$_min_length.'位！');
	return _escape_string($_content);
}

/**
 * _get_title 截取标题
 * @param unknown $_string
 * @param unknown $_strlen
 * @return string
 */
function _get_title($_string,$_strlen) {
	if (mb_strlen($_string,'utf-8') > $_strlen) {
		$_string = mb_substr($_string,0,$_strlen,'utf-8').'...';
	}
	return $_string;
}

/**
 * 验证唯一标识符是否一致
 * @param unknown $_uniqid
 * @param unknown $_another
 * @return boolean
 */
function _is_uniqid_consistent($_uniqid, $_another) {
	if($_uniqid == $_another) 
		return true;
	else 
		return false;
}

/**
 * _check_content 添加好友时的验证信息
 * @param unknown $_content
 * @param unknown $_min_length
 */
function _check_content($_content, $_min_length, $_max_length) {
	$_content = trim($_content);
	if(mb_strlen($_content, 'utf-8') < $_min_length || mb_strlen($_content, 'utf-8') > $_max_length)
		_alert_back('好友验证信息不得小于'.$_min_length.'位，或大于'.$_max_length.'位！');
	return _escape_string($_content);
}
/**
 * _alert_back()弹窗提示，并返回历史记录
 * @access public 
 * @param unknown $_info
 */
function _alert_back($_info) {
	echo "<script type='text/javascript'>alert('$_info');history.back();</script>";
	exit();
}

/**
 * _alert_close 弹窗提示，并关闭窗口
 * @param unknown $_info
 */
function _alert_close($_info) {
	echo "<script type='text/javascript'>alert('$_info');window.close();</script>";
	exit();
}

function _confirm($_info) {
	echo "<script type='text/javascript>if(!window.confirm('$_info')) history.back();</script>";
}

/**
 * _escape_string返回即将存入数据库中的转义后的字符串
 * @param unknown $_string
 * @return string|unknown
 */
function _escape_string($_string) {
	if(!GPC) {
		return mysql_real_escape_string($_string);
	}
	return $_string;
}

/**
 * _html_chars 转义html字符
 * @param unknown $_string
 */
function _html_chars($_string) {
	if(is_array($_string)) {
		foreach($_string as $_key=>$_value) {
			$_string[$_key] = _html_chars($_value);
		}
	} else {
		$_string = htmlspecialchars($_string);
	}
	return $_string;
}

/**
 * _location 实现跳转功能
 * @param unknown $_info
 * @param unknown $_url
 */
function _location($_info, $_url) {
	if($_info == null) {
		header('Location:'.$_url);
	} else {
		echo "<script type='text/javascript'>alert('$_info');location.href='$_url';</script>";
		exit();
	}
}

/**
 * _connect_server 连接数据库服务器
 * @param unknown $_host
 * @param unknown $_user
 * @param unknown $_password
 */
function _connect_server($_host, $_user, $_password) {
	global $_conn;
	if(!$_conn = mysql_connect($_host, $_user, $_password)) {
		die('数据库连接失败'.mysql_error());
	}
}

/**
 * _select_db 选择数据库
 * @param unknown $_dbname
 */
function _select_db($_dbname) {
	if(!mysql_select_db($_dbname)) {
		die('指定的数据库不存在！'.mysql_error());
	}
}

/**
 * _select_charset 设置字符集
 * @param unknown $_charset
 */
function _select_charset($_charset) {
	if(!mysql_query("SET NAMES ".$_charset)) {
		die('字符集设置错误！'.mysql_error());
	}
}

/**
 * _set_login_cookies 生成登录cookie
 * @param unknown $_username
 * @param unknown $_uniqid
 * @param unknown $_time
 */
function _set_login_cookies($_username, $_uniqid, $_time) {
	switch($_time) {
		case '0' : //浏览器进程
			setcookie('username', $_username);
			setcookie('uniqid', $_uniqid);
			break;
		case '1' : //一天
			setcookie('username', $_username, time()+86400);
			setcookie('uniqid', $_uniqid, time()+86400);
			break;
		case '2' : //一周
			setcookie('username', $_username, time()+604800);
			setcookie('uniqid', $_uniqid, time()+604800);
			break;
		case '3' : //一月
			setcookie('username', $_username, time()+2592000);
			setcookie('uniqid', $_uniqid, time()+2592000);
			break;

	}
}

/**
 * unset_login_cookies 销毁登录cookie
 */
function _unset_login_cookies() {
	setcookie('username', '', time()-1);
	setcookie('uniqid', '', time()-1);
}

/**
 * _is_login 判断是否登录过
 * @return boolean
 */
function _is_login() {
	if(isset($_COOKIE['username']) && isset($_COOKIE['uniqid']))
		return true;
	return false;
}

/**
 * _get_xml 获取xml数据
 * @param unknown $_xmlfile
 * @return Ambigous <unknown, string>
 */
function _get_xml($_xmlfile) {
	$_data = array();
	if (file_exists($_xmlfile)) {
		$_xml = file_get_contents($_xmlfile);
		preg_match_all('/<vip>(.*)<\/vip>/s',$_xml,$_dom);
		foreach ($_dom[1] as $_value) {
			preg_match_all('/<id>(.*)<\/id>/s',$_value,$_id);
			preg_match_all('/<username>(.*)<\/username>/s',$_value,$_username);
			preg_match_all( '/<sex>(.*)<\/sex>/s', $_value, $_sex);
			preg_match_all( '/<face>(.*)<\/face>/s', $_value, $_face);
			preg_match_all( '/<email>(.*)<\/email>/s', $_value, $_email);
			$_data['id'] = $_id[1][0];
			$_data['username'] = $_username[1][0];
			$_data['sex'] = $_sex[1][0];
			$_data['face'] = $_face[1][0];
			$_data['email'] = $_email[1][0];
		}
	} else {
		$_data['id'] = $_data['username'] = $_data['sex'] = $_data['face'] = $_data['email'] = "Error:404";
	}
	return _html_chars($_data);
}


/**
 * _new_xml 新建一个xml文件，将数据写入到xml中
 * @param unknown $_xmlfile
 * @param unknown $_data
 */
function _new_xml($_xmlfile, $_data) {
	//在写模式下打开一个文件，如果不存在就新建一个
	$_fp = @fopen($_xmlfile, 'w');
	if(!$_fp)
		exit('系统错误：文件不存在！');
	flock($_fp, LOCK_EX);
	$_string = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\r\n";
	fwrite($_fp, $_string, strlen($_string));
	$_string = "<vip>\r\n";
	fwrite($_fp, $_string, strlen($_string));
	$_string = "\t<id>{$_data['id']}</id>\r\n";
	fwrite($_fp, $_string, strlen($_string));
	$_string = "\t<username>{$_data['username']}</username>\r\n";
	fwrite($_fp, $_string, strlen($_string));
	$_string = "\t<sex>{$_data['sex']}</sex>\r\n";
	fwrite($_fp,$_string,strlen($_string));
	$_string = "\t<face>{$_data['face']}</face>\r\n";
	fwrite($_fp,$_string,strlen($_string));
	$_string = "\t<email>{$_data['email']}</email>\r\n";	
	fwrite($_fp,$_string,strlen($_string));
	$_string = "</vip>";
	fwrite($_fp, $_string, strlen($_string));
	flock($_fp, LOCK_UN);
	fclose($_fp);
}


/**
 * _init_page 初始化一个分页的相关参数
 * @param unknown $_sql
 * @param unknown $_page_size
 */
function _init_page($_sql, $_page_size) {
	//获得总页数：如果数据总共为0行，则总页数为1；否则，为行数除以每页大小向上取整
	$GLOBALS['page_size'] = $_page_size;
	$GLOBALS['all_rows'] = mysql_num_rows(mysql_query($_sql));
	if($GLOBALS['all_rows'] == 0) {
		$GLOBALS['all_pages'] = 1;
	} else {
		$GLOBALS['all_pages'] = ceil($GLOBALS['all_rows'] / $_page_size);
	}
	
	//获得当前的页码：页数为空或者小于等于0或者不为数字时，系统默认当前的页码位第一页；当输入的页码大于总页数时，默认为最后一页；否则为输入的页码的整数值
	if (isset($_GET['page'])) {
		$GLOBALS['curr_page'] = $_GET['page'];
		if (empty($GLOBALS['curr_page']) || $GLOBALS['curr_page'] <= 0 || !is_numeric($GLOBALS['curr_page'])) {
			$GLOBALS['curr_page'] = 1; 
		} else if($GLOBALS['curr_page'] > $GLOBALS['all_pages']) {
			$GLOBALS['curr_page'] = $GLOBALS['all_pages'];
		} else {
			$GLOBALS['curr_page'] = intval($GLOBALS['curr_page']);
		}
	} else {
		$GLOBALS['curr_page'] = 1;
	}
	
	//获得当前页面的起始行数
	$GLOBALS['start_row'] = ($GLOBALS['curr_page'] - 1) * $GLOBALS['page_size'];
}


/**
 * _show_page 显示分页
 * @param unknown $_type
 * @param unknown $_param
 */
function _show_page($_type, $_param) {
	if ($_type == 1) {
		echo '<div id="page_num">';
		echo '<ul>';
		for ($i = 0; $i < $GLOBALS['all_pages']; $i++) {
			if ($GLOBALS['curr_page'] == ($i + 1)) {
				echo '<li><a href="'.SCRIPT.'.php?page='.($i+1).''.$_param.'" class="selected">'.($i+1).'</a></li>';
			} else {
				echo '<li><a href="'.SCRIPT.'.php?page='.($i+1).''.$_param.'">'.($i+1).'</a></li>';
			}
		}
		echo '</ul>';
		echo '</div>';
	} elseif ($_type == 2) {
		echo '<div id="page_text">';
		echo '<ul>';
		echo '<li>'.$GLOBALS['curr_page'].'/'.$GLOBALS['all_pages'].'页 | </li>';
		echo '<li>共有<strong>'.$GLOBALS['all_rows'].'</strong>条数据 | </li>';
		if ($GLOBALS['curr_page'] == 1) {
			echo '<li>首页 | </li>';
			echo '<li>上一页 | </li>';
		} else {
			echo '<li><a href="'.SCRIPT.'.php">首页</a> | </li>';
			echo '<li><a href="'.SCRIPT.'.php?page='.($GLOBALS['curr_page'] - 1).''.$_param.'">上一页</a> | </li>';
		}
		if ($GLOBALS['curr_page'] == $GLOBALS['all_pages']) {
			echo '<li>下一页 | </li>';
			echo '<li>尾页</li>';
		} else {
			echo '<li><a href="'.SCRIPT.'.php?page='.($GLOBALS['curr_page'] + 1).''.$_param.'">下一页</a> | </li>';
			echo '<li><a href="'.SCRIPT.'.php?page='.$GLOBALS['all_pages'].''.$_param.'">尾页</a></li>';
		}
		echo '</ul>';
		echo '</div>';
	} else {
		_show_page(2, $_param);
	}
}

/**
 * _ubb 進行ubb解析和排版
 * @param unknown $_string
 * @return mixed
 */
function _ubb($_string) {
	$_string = nl2br($_string);
	$_string = preg_replace('/\[size=(.*)\](.*)\[\/size\]/U','<span style="font-size:\1px">\2</span>',$_string);
	$_string = preg_replace('/\[b\](.*)\[\/b\]/U','<strong>\1</strong>',$_string);
	$_string = preg_replace('/\[i\](.*)\[\/i\]/U','<em>\1</em>',$_string);
	$_string = preg_replace('/\[u\](.*)\[\/u\]/U','<span style="text-decoration:underline">\1</span>',$_string);
	$_string = preg_replace('/\[s\](.*)\[\/s\]/U','<span style="text-decoration:line-through">\1</span>',$_string);
	$_string = preg_replace('/\[color=(.*)\](.*)\[\/color\]/U','<span style="color:\1">\2</span>',$_string);
	$_string = preg_replace('/\[url\](.*)\[\/url\]/U','<a href="\1" target="_blank">\1</a>',$_string);
	$_string = preg_replace('/\[email\](.*)\[\/email\]/U','<a href="mailto:\1">\1</a>',$_string);
	$_string = preg_replace('/\[img\](.*)\[\/img\]/U','<img src="\1" alt="图片" />',$_string);
	$_string = preg_replace('/\[flash\](.*)\[\/flash\]/U','<embed style="width:480px;height:400px;" src="\1" />',$_string);
	return $_string;
}

?>