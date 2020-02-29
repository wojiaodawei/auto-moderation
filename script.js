function checkForm() {

	// Fetching values from all input fields and storing them in variables.
	var title = document.getElementById("title1").value;
	var message = document.getElementById("message1").value;

	//Check input Fields Should not be blanks.
	if (title == '' || message == '') {
		alert("Fill All Fields");
	} else {
		//Notifying error fields
		var title1 = document.getElementById("title");
		var message1 = document.getElementById("message");
		//Check All Values/Informations Filled by User are Valid Or Not.If Fields Are invalid Then Generate alert.
		if (!(title1.innerHTML.includes("span>")) || !(message1.innerHTML.includes("span>"))) {
			alert("Fill Valid Information");
		} else {
			//Submit Form When All values are valid.
			document.getElementById("myForm").submit();
		}
	}
}

// AJAX code to check input field values when event triggered.
function validate(field, query) {
	var xmlhttp;

	if (window.XMLHttpRequest) { // for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	} else { // for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState != 4 && xmlhttp.status == 200) {
			document.getElementById(field).innerHTML = "Validating..";
		} else if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			document.getElementById(field).innerHTML = xmlhttp.responseText;
		} else {
			document.getElementById(field).innerHTML = "";
			//document.getElementById(field).innerHTML = "Error Occurred. <a href='index.php'>Reload Or Try Again</a> the page.";
		}
	}

	xmlhttp.open("GET", "pre_moderation.php?field=" + field + "&query=" + query, false);
	xmlhttp.send();
}
