<?php
/**
* Author: Cathy
* Date: 2014-11-24
*/
//防止恶意调用
if(!defined('ACCESS')) {
	exit('Failure to access!');
}
?>
<div id="footer">
	<p>本程序执行耗时为：<?php echo round(_time_now() - START_TIME, 4); ?>秒</p>
	<p>Copyright © 2014 <span>Cathy Zhang</span></p>
</div>
