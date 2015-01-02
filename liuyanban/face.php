<?php
/**
* Author: Cathy
* Date: 2014-11-27
*/
define("ACCESS", true);

define("SCRIPT", "face");

require dirname(__FILE__).'/include/common.inc.php';

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cathy的简易论坛系统--头像</title>
<?php include ROOT_PATH.'/import.inc.php';?>
<script type="text/javascript" src="js/face.js"></script>
</head>
<body>

<div id="face">
	<h3>选择头像</h3>
	<dl>
		<?php foreach(range(1, 9) as $_number) { ?>
		<dd><img title="头像<?=$_number?>" alt="face/m0<?=$_number?>.jpg" src="face/m0<?=$_number?>.jpg"/></dd>
		<?php } ?>
	</dl>
	<dl>
		<?php foreach(range(10, 64) as $_number) { ?>
		<dd><img title="头像<?=$_number?>" alt="face/m<?=$_number?>.jpg" src="face/m<?=$_number?>.jpg"/></dd>
		<?php } ?>
	</dl>
</div>

</body>
</html>