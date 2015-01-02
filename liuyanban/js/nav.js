/**
 * 
 */
var skin = document.getElementById('skin');
if(skin) {
	var skin_ul = skin.getElementsByTagName('ul')[0];
	skin.onmouseover = function() {
		skin_ul.style.display = 'block';
	};
	skin.onmouseout = function() {
		skin_ul.style.display = 'none';
	};
}

var me = document.getElementById('me');
if(me) {
	var name_ul = me.getElementsByTagName('ul')[0];
	me.onmouseover = function() {	
		name_ul.style.display = 'block';
	};
	me.onmouseout = function() {	
		name_ul.style.display = 'none';
	};
}




