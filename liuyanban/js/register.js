/**
 * 
 */
window.onload = function() {

	//更换头像时打开一个子窗口
	face_window();
	function face_window() {
		var face = document.getElementById("faceimg");
		if(face != null) {
			face.onclick = function() {
				window.open("face.php", "face", "width=400,height=400,top=0,left=0,scrollbars=1" );
			}
		}	
	}
	
	//验证码更新
	update_code();
	
	//注册表单验证
	check_register_form();
}