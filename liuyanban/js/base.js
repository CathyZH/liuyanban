
/**
 * 用户名验证
 */
function check_username(username) {
	if(username.value.length < 2 || username.value.length > 20) {
		alert('用户名不得小于2位或大于20位！');
		username.value = '';
		username.focus();
		return false; //用户名不满足条件是就退出
	} 
	if(/[<>\'\"\ \	]/.test(username.value)) {
		alert('用户名不得包含非法字符！');
		username.value = '';
		username.focus();
		return false; //用户名不满足条件是就退出
	}
	return true;
}
/**
 * 密码验证(包括一致性验证)
 */
function check_password(password, notpassword) {
	//密码验证
	if(password.value.length < 6) {
		alert('密码不得小于6位！');
		password.value = '';
		password.focus();
		return false; 
	}
	//密码一致性验证
	if(password.value != notpassword.value) {
		alert('密码不一致！');
		notpassword.value = '';
		notpassword.focus();
		return false; 
	}
	return true;
}

/**
 * 密码提示验证
 */
function check_question(question) {
	if(question.value.length < 2 || question.value.length > 20) {
		alert('密码提示不得小于2位或大于20位！');
		question.value = '';
		question.focus();
		return false; //防止表单提交出去
	}
	return true;
}

/**
 * 密码提示回答验证
 */
function check_answer(answer, question) {
 	if(answer.value.length < 2 || answer.value.length > 20) {
		alert('您的回答不得小于2位或大于20位！');
		answer.value = '';
		answer.focus();
		return false; //防止表单提交出去
	}
	if(question.value == answer.value) {
		alert('密码提示与回答不能相同！');
		answer.value = '';
		answer.focus();
		return false; //防止表单提交出去
	}
	return true;
}

/**
 * 邮箱验证
 */
function check_email(email) {
 	if(!/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/.test(email.value)) {
		alert('邮箱格式不正确！');
		email.value = '';
		email.focus();
		return false; //防止表单提交出去
	}
	return true;
}
 
 /**
  * 验证码长度验证
  */
function check_code(code) {
	if(code.value.length != 4) {
		alert('验证码长度必须为4位！');
		code.value = '';
		code.focus();
		return false;
	}
	return true; 
}

 /**
 * 注册表单验证
 */
function check_register_form() {
	var form = document.getElementsByTagName("form")[0];
	if(form != null){
		//在表单提交时进行验证
		var username = form.username;
		var password = form.password;
		var notpassword = form.notpassword;
		var question = form.question;
		var answer = form.answer;
		var email = form.email;
		var code = form.code;
		form.onsubmit = function() {
			if( check_username(username) &&
				check_password(password, notpassword) &&
				check_question(question) &&
				check_answer(answer, question) &&
				check_email(email) &&
				check_code(code) 
			) return true;
			
			return false;			
		}
	}		
}
/****************************************************************************/

/**
 * 标题验证
 */
function check_title(title) {
	if (title.value.length < 2 || title.value.length > 40) {
		alert('标题不得小于2位或者大于40位');
		title.value = ''; //清空
		title.focus(); //将焦点以至表单字段
		return false;
	}
	return true;
}
/**
 * 内容验证
 */
function check_content(content) {
	if (content.value.length < 10) {
		alert('内容不得小于10位');
		content.value = ''; //清空
		content.focus(); //将焦点以至表单字段
		return false;
	}
	return true;
}
/**
 * 主题表单验证
 */
function　check_topic_form() {
	var form = document.getElementsByTagName("form")[0];
	if(form != null) {
		var title = form.title;
		var content = form.content;
		var code = form.code; 
		form.onsubmit = function() {
			if(	check_title(title) &&
				check_content(content) &&
				check_code(code)				
			) return true;
			
			return false;
		}
	}
}

/****************************************************************/

function check_content(content) {
	if(content.value.length < 10 || content.value.length > 200) {
		alert('好友验证信息不得小于10位或者大于200位！');
		content.value = ''; //清空
		content.focus(); //将焦点以至表单字段
		return false;
	}
	return true;
}

/****************************************************************/

/**
 * 更新验证码
 */
function update_code() {
	var code = document.getElementById("code");
	if(code) {
		code.onclick = function() {
			this.src = "code.php?tm="+Math.random();
		}
	}
} 

/*******************************************************************/

function centerWindow(url,name,height,width) {
	var left = (screen.width - width) / 2;
	var top = (screen.height - height) / 2;
	window.open(url,name,'height='+height+',width='+width+',top='+top+',left='+left);
}

/**
 * 私人留言
 */
function send_message() {
	var message = document.getElementsByName('message');
	if(message != null && message.length != 0) {
		for (var i = 0; i < message.length; i++) {
			message[i].onclick = function () {
				centerWindow('s_message.php?id='+this.title,'message',250,400);
			};
		}
	}
}

/**
 * 添加好友
 */
function add_friend() {
	var friend = document.getElementsByName('friend');
	if(friend != null && friend.length != 0) {
		for (var i = 0; i < friend.length; i++) {
			friend[i].onclick = function () {
				centerWindow('a_friend.php?id='+this.title,'friend',250,400);
			};
		}
	}
}
