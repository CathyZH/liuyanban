window.onload = function() {
	//验证码更新
	update_code();
	
	//主题表单验证
	check_topic_form();
	
	//q表情
	q_expression();
	
	//ubb操作
	ubb();
	
	//监听发送私人留言事件
	send_message();
	
	//监听加好友事件
	add_friend();
}