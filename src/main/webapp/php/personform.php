<?php
header("Origin: http://localhost/");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Form for request authors or contributors personal data</title>
<link  rel="stylesheet" type="text/css" href="../css/style.css"/>
</head>
<body>
<h1>Eingabeformular</h1>
<p>Bitte geben Sie Autoren und andere Beteiligte für die hochzuladende Publikation ein</p> 
<form method="get" action="">
	<table>
		<tr>
			<td>Akad. Titel</td>
			<td>Vorname</td>
			<td>Nachname</td>
			<td>PND ID</td>
			<td>Rolle</td>
		</tr>
		<tr>
			<td><input class="academicTitle" name="acadTitle" type="text" size="5"/></td>
			<td><input class="firstName" type="text" size="20" value="Andres" /></td>
			<td><input class="lastName" type="text" size="20" value="Quast" /></td>
			<td><input class="PNDIdentNumber" type="text" size="10"/></td>
			<td><select>
			<option value="author">Autor</option>
			<option value="editor">Editor</option>
			<option value="contributor">Beteiligter</option>
			<option value="reviewer">Gutachter</option>
			</select></td>
		</tr>
		<tr>
			<td><input class="academicTitle" name="acadTitle" type="text" size="5"/></td>
			<td><input class="firstName" type="text" size="20"/></td>
			<td><input class="lastName" type="text" size="20"/></td>
			<td><input class="PNDIdentNumber" type="text" size="10"/></td>
			<td><select>
			<option value="author">Autor</option>
			<option value="editor">Editor</option>
			<option value="contributor">Beteiligter</option>
			<option value="reviewer">Gutachter</option>
			</select></td>
		</tr>
	</table>
<input type="submit" value="weitere Person"/>
	<input type="submit" value="...und weg damit"/>
	
</form> 

<div class="result">
</div>
<script src="../js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="../js/pndId.js" type="text/javascript"></script>
</body>
</html>
<?php
?>