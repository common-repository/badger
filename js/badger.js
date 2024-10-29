(function() {

	var origTitle = document.title;
	var newflag = 0;

	if (php_vars.suffixindex === 0) {
		var altTitle = php_vars.alttitle;
	} else {
		var altTitle = php_vars.alttitle + origTitle;
	}

	window.addEventListener("focus", function() {
		clearInterval(timerSwitchTitle);
		document.title = origTitle;
	});

	window.addEventListener("blur", function() {
		var timerSwitchTitle = setInterval(function() {
			if (newflag === 0) {
				document.title = altTitle;
				newflag = 1;
			} else {
				document.title = origTitle;
				newflag = 0;
			}
		}, 1000);
	});

})();
