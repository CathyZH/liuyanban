window.onload = function() {
	//验证码
	update_code();
	
	//验证表单
	var form = document.getElementsByTagName('form')[0];
	if(form) {
		form.onsubmit = function() {
			return 
				check_content(this.content) &&
				check_code(this.code);
		}		
	}
};