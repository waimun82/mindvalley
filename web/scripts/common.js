function showDIV(id) {
	document.getElementById(id).style.display = 'block';
}

function hideDIV(id) {
	document.getElementById(id).style.display = 'none';
}

function clearForm(form) {
	tags = form.getElementsByTagName('input');
	for(counter = 0; counter < tags.length; counter++) {
		switch(tags[counter].type) {
			case 'password':
			case 'text':
				tags[counter].value = '';
				break;
			case 'checkbox':
			case 'radio':
				tags[counter].checked = false;
				break;
		}
	}

	tags = form.getElementsByTagName('select');
	for(counter = 0; counter < tags.length; counter++) {
		tags[counter].selectedIndex = 0;
	}

	tags = form.getElementsByTagName('textarea');
	for(counter = 0; counter < tags.length; counter++) {
		tags[counter].value = '';
	}

}

function copyToClipboard(text) {
  window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
}

function loading() {
	document.getElementById('loading').style.display = 'inline';
	document.getElementById('loading').innerHTML = '<img id="loading-image" src="templates/images/shared/loading.gif" alt="Loading..." />';
}

function refreshWindow(flag) {
	if(flag = true) {
		setTimeout(function(){
			window.location.reload(0);
		}, 5000);
	}
}