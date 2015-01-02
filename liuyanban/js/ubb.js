/**
 * 设置表单中content字段的值
 */
function content(form, string) {
	if(form.content)
		form.content.value += string; 
} 

//在页面中直接调用	
function font(size) {
	document.getElementsByTagName('form')[0].content.value += '[size='+size+'][/size]';
}
//在页面中直接调用
function showcolor(value) {
	document.getElementsByTagName('form')[0].content.value += '[color='+value+'][/color]';
}


	

/**
 * ubb操作
 */
function ubb() {
	var html = document.getElementsByTagName('html')[0];
	html.onmouseup = function () {
		font.style.display = 'none';
		color.style.display = 'none';
	};
		
	var form = document.getElementsByTagName('form')[0];
	if(form != null) {
		
	} 
	
	var ubb = document.getElementById('ubb');
	if (ubb != null) {
		var ubbimg = ubb.getElementsByTagName('img');
		
		var font = document.getElementById('font');
		ubbimg[0].onclick = function() {
			if(font)
				font.style.display = 'block';
		};
		
		
		ubbimg[2].onclick = function () {
			content(form, '[b][/b]');
		};
		ubbimg[3].onclick = function () {
			content(form, '[i][/i]');
		};
		ubbimg[4].onclick = function () {
			content(form, '[u][/u]');
		};
		ubbimg[5].onclick = function () {
			content(form, '[s][/s]');
		};
		
		var color = document.getElementById('color');
		ubbimg[7].onclick = function() {
			if(color) {
				color.style.display = 'block';
				form.t.focus();
			}	
		};
		form.t.onclick = function () {
			showcolor(this.value);
		}
		
		ubbimg[8].onclick = function () {
			var url = prompt('请输入网址：','http://');
			if (url) {
				if (/^https?:\/\/(\w+\.)?[\w\-\.]+(\.\w+)+/.test(url)) {
					content(form, '[url]'+url+'[/url]');
				} else {
					alert('网址不合法！');
				}
			}
		};
		ubbimg[9].onclick = function () {
			var email = prompt('请输入电子邮件：','@');
			if (email) {
				if (/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/.test(email)) {
					content(form, '[email]'+email+'[/email]');
				} else {
					alert('电子邮件不合法！');
				}
			}
		};
		ubbimg[10].onclick = function () {
			var img = prompt('请输入图片地址：','');
			if (img) {
				content(form, '[img]'+img+'[/img]');
			}
		};
		ubbimg[11].onclick = function () {
			var flash = prompt('请输入视频flash：','http://');
			if (flash) {
				if (/^https?:\/\/(\w+\.)?[\w\-\.]+(\.\w+)+/.test(flash)) {
					content(form, '[flash]'+flash+'[/flash]');
				} else {
					alert('视频不合法！');
				}
			}
		};
		ubbimg[18].onclick = function () {
			if(form.content)
				form.content.rows += 2;
		};
		ubbimg[19].onclick = function () {
			if(form.content)	
				form.content.rows -= 2;
		};
				
	}
}

/******************************************************************************/

//q表情
function q_expression() {
	var q = document.getElementById('q');
	if (q != null) {
		var qa = q.getElementsByTagName('a');
	
		qa[0].onclick = function() {
			window.open('qexp.php?num=48&path=qexp/1/','qexp','width=400,height=400,scrollbars=1');
		};
		qa[1].onclick = function() {
			window.open('qexp.php?num=10&path=qexp/2/','qexp','width=400,height=400,scrollbars=1');
		};
		qa[2].onclick = function() {
			window.open('qexp.php?num=39&path=qexp/3/','qexp','width=400,height=400,scrollbars=1');
		};
	}
}

	
	
	