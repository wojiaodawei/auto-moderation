<!DOCTYPE html>
<html>
	<head>
		<link href="style.css" rel="stylesheet" type="text/css">
		<script src="script.js"></script>
	</head>

	<body>
		<div id="mainform">
			<div class="innerdiv"></div>
				<!-- interesting user input events: onblur, onchange, onkeyup -->
				<form action='#' id="myForm" method='post' name="myForm">
					<h3>Fill the form!</h3>
					<table>
						<tr>
							<td>Title</td>
							<td><input id='title1' name='title' onchange="validate('title', this.value)" type='text'></td>
							<td><div id='title'></div></td>
						</tr>
						<tr>
							<td>Description</td>
							<td><textarea id='message1' name="message" rows="10" cols="30" onkeyup="validate('message', this.value)" type='text'></textarea></td>
							<td><div id='message'></div></td>
						</tr>
					</table>
					<input onclick="checkForm()" type='button' value='Submit'>
					<?php
						if (!empty($_POST)) echo "<span>Submitted!</span>";
					?>
				</form>
		</div>
	</body>
</html>
