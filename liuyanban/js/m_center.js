window.onload = function() {
	//验证码更新
	update_code();
	
	//验证表单
	var form = document.getElementsByTagName("form")[0];
	if(form != null){
		var password = form.password;
		var email = form.email;
		var code = form.code;
		if(password.value) {
			return 
				check_password(password, notpassword) &&
				check_email(email) &&
				check_code(code);
		} else {
			return 
				check_email(email) &&
				check_code(code);
		}
	}
}