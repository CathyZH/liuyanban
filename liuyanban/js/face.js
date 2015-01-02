/**
 * 
 */
window.onload = function() {
	
	function call_opener(src) {
		//opener表示父窗口节点对象，说明该函数是在子窗口中被调用的
		opener.document.getElementById('faceimg').src = src; 
		opener.document.register.face.value = src; //设置名字为register表单下的名字为face的字段的值为src
	}
	/**
	 * 选择一个头像
	 */
	function select_face() {
		var images = document.getElementsByTagName("img");
		if(images != null && images.length !=0) {
			for(var i=0; i<images.length; i++) {
				images[i].onclick = function() {
					call_opener(this.alt);
				}
			}
		}
	}
		
	select_face();

}