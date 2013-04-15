<%@ page language="java" contentType="text/html; charset=UTF-8"
    pageEncoding="UTF-8"%>
<%
int z;
if(request.getParameter("count")!= null){
	String count = request.getParameter("count");
	z = Integer.parseInt(count);
	}else{
		z = 1;
	}
String[] acadTitle = new String[z];
String[] fName = new String[z];
String[] lName = new String[z];
String[] pndId = new String[z];
for(int i= 0; i<z; i++){
	if(request.getParameter("acadTitle" + i )!= null){
		acadTitle[i] = request.getParameter("acadTitle" + i);
	}else{
		acadTitle[i] = "";
	}
	if(request.getParameter("firstName" + i )!= null){
		fName[i] = request.getParameter("firstName" + i);
	}else{
		fName[i] = "";
	}
	if(request.getParameter("lastName" + i )!= null){
		lName[i] = request.getParameter("lastName" + i);
	}else{
		lName[i] = "";
	}
	if(request.getParameter("pndId" + i )!= null){
		pndId[i] = request.getParameter("pndId" + i);
	}else{
		pndId[i] = "";
	}
}

%>


<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>bibliographic data input form</title>
<link href="../css/style.css" rel="stylesheet" type="text/css" />
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
		</tr>
<% 
for(int i = 0; i < z ; i++ ){

%>
		<tr>
			<td><input class="academicTitle" name="acadTitle<%= i %>" type="text" size="5" value="<%= acadTitle[i] %>"/></td>
			<td><input class="firstName" name="firstName<%= i %>" type="text" size="20" value="<%= fName[i] %>"/></td>
			<td><input class="lastName" name="lastName<%= i %>" type="text" size="20" value="<%= lName[i] %>"/></td>
			<td><input class="PNDIdentNumber" name="pndId<%= i %>" type="text" size="10" value="<%= pndId[i] %>"/></td>
			<td><select>
			<option value="author">Autor</option>
			<option value="editor">Editor</option>
			<option value="contributor">Beteiligter</option>
			<option value="reviewer">Gutachter</option>
			</select></td>
		</tr>
<%
}
%>
	</table>
    <input type="hidden" name="count" value="<%= z + 1 %>"/>
    <input type="submit" value="weitere Person" name="addPerson"/>
	<input type="submit" value="...und weg damit" name="save"/>
	</ul>
</form> 
<script src="../js/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="../js/gndRequest.js" type="text/javascript"></script>
</body>
</html>