<!DOCTYPE HTML>
<html>
	<head>
		<title>musicXML converter</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
		<script src="progress.js"></script>
	</head>
	<body>
		<h1>Convert musicXML into scratch lists</h1>
		<form action="upload.php" method="post" enctype="multipart/form-data">
			<input type="file" id="mxlxml" name="mxlxml" accept="application/vnd.recordare.musicxml|application/vnd.recordare.musicxml+xml" />
			<input type="submit" value="Start" />
		</form>
		<progress id="upload" value="0"></progress>
		<div id="file">
			<table>
				<tr>
					<td>Name:</td>
					<td id="name">Nothing selected</td>
				</tr>
				<tr>
					<td>Size:</td>
					<td id="size">Nothing selected</td>
				</tr>
				<tr>
					<td>Type:</td>
					<td id="mime">Nothing selected</td>
				</tr>
			</table>
		</div>
	</body>
</html>