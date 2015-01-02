<?php
/**
* Author: Cathy
* Date: 2014-11-25
*/
if(!defined('ACCESS')) {
	exit('Failure to access!');		
}

//防止非HTML页面调用
if(!defined('SCRIPT')) {
	exit('Script Error!');
}

?>
<link rel="shortcut icon" href="img/favicon.ico" />
<link rel="stylesheet" type="text/css" href="style/basic.css" />
<link rel="stylesheet" type="text/css" href="style/<?php echo SCRIPT?>.css" />
<script type="text/javascript" src="js/base.js"></script>
