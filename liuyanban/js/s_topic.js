window.onload = function() {
	//验证码更新
	update_code();
	
	//主题表单验证
	check_topic_form();
	
	//q表情
	q_expression();
	
	//ubb操作
	ubb();
	
	//点击某一楼层的回复锚点时，将回复表单的标题改为“回复某某楼的谁谁谁”
	change_title();
	function change_title() {
		var re = document.getElementsByName('re');
		if(re != null && re.length != 0) {
			for(var i = 0; i < re.length; i++)
				re[i].onclick = function() {
					var form = document.getElementsByTagName('form')[0];
					if(form) form.title.value = this.title;
	
			}
		}
	}
	
	//监听发送私人留言事件
	send_message();
	
	//监听加好友事件
	add_friend();
	
}