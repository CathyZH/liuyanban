<?php
/**
* Author: Cathy
* Date: 2014-11-26
*/
//定义常量，用来授权调用includes里面的文件
define('ACCESS', true);

//引入公共文件，通过硬路径引入速度更快
require_once dirname(__FILE__).'/include/common.inc.php';

//生成验证码，并将新生成的code放到session中
_generate_code();

?>