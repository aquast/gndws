<%@page import="java.util.ArrayList"%>
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


ArrayList<String> acadTitleList = new ArrayList<String>();
for (int i=0; i<z; i++){
	acadTitleList.add(i,"");
}

if(request.getParameter("acadTitle[]")!= null && request.getParameter("acadTitle[]").length()!=0){
	String[] acadTitle = request.getParameterValues("acadTitle[]");
	for (int i=0; i<acadTitle.length; i++){
		acadTitleList.set(i,acadTitle[i]);
	}
}

ArrayList<String> firstNameList = new ArrayList<String>();
for (int i=0; i<z; i++){
	firstNameList.add(i,"");
}

if(request.getParameter("firstName[]")!= null && request.getParameter("firstName[]").length()!=0){
	String[] firstName = request.getParameterValues("firstName[]");
	for (int i=0; i<firstName.length; i++){
		firstNameList.set(i,firstName[i]);
	}
}

ArrayList<String> lastNameList = new ArrayList<String>();
for (int i=0; i<z; i++){
	lastNameList.add(i,"");
}

if(request.getParameter("lastName[]")!= null && request.getParameter("lastName[]").length()!=0){
	String[] lastName = request.getParameterValues("lastName[]");
	for (int i=0; i<lastName.length; i++){
		lastNameList.set(i,lastName[i]);
	}
}

ArrayList<String> pndIdList = new ArrayList<String>();
for (int i=0; i<z; i++){
	pndIdList.add(i,"");
}

if(request.getParameter("pndId[]")!= null && request.getParameter("pndId[]").length()!=0){
	String[] pndId = request.getParameterValues("pndId[]");
	for (int i=0; i<pndId.length; i++){
		pndIdList.set(i,pndId[i]);
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
	<h1>Eingabeformular für Publikationen</h1>
	<div class="requestarea">
		<form method="get" action="">
			<div class="formarea">
				<p>Bitte geben Sie den Titel der Publikation an</p>
				<table>
					<tr>
						<td>Titel</td>
					</tr>
					<tr>
						<td><input class="Title" name="Title" type="text" size="70" />
						</td>
					</tr>
					<table>

					</table>
				</table>
			</div>
			<div class="formarea">
				<p>Bitte geben Sie Autoren und andere Beteiligte für die
					hochzuladende Publikation ein</p>
				<table>
					<tr>
						<td>Akad. Titel</td>
						<td>Vorname(n)</td>
						<td>Nachname</td>
						<td>PND ID</td>
						<td>Rolle</td>
					</tr>
					<% 
for(int i = 0; i < z ; i++ ){

%>
					<tr>
						<td><input class="academicTitle" name="acadTitle[]"
							type="text" size="10" value="<%= acadTitleList.get(i) %>" />
						</td>
						<td><input class="firstName" name="firstName[]" type="text"
							size="20" value="<%= firstNameList.get(i) %>" />
						</td>
						<td><input class="lastName" name="lastName[]" type="text"
							size="20" value="<%= lastNameList.get(i) %>" />
						</td>
						<td><input class="PNDIdentNumber" name="pndId[]" type="text"
							size="10" value="<%= pndIdList.get(i) %>" /> 
							<input type="hidden" name="index" value="<%= i %>" />
						</td>
						<td><select>
								<option value="author">Autor</option>
								<option value="editor">Editor</option>
								<option value="contributor">Beteiligter</option>
								<option value="reviewer">Gutachter</option>
						</select>
						</td>
					</tr>
					<%
}
%>
				</table>
			</div>

			<input type="hidden" name="count" value="<%= z + 1 %>" />
			
			<input type="submit" value="weitere Person" name="addPerson" /> 
			<input type="submit" value="...und weg damit" name="save" />
			<ul></ul>
		</form>
		<form method="get" action="">
			<input type="submit" value="Formular verwerfen" name="clear" />
		</form>
	</div>
	
	<div class="resultarea">
	<p>Ergebnisse</p>
	</div>
<script src="../js/jquery-1.9.1.min.js"></script>
<script src="../js/gndInRequest.js" type="text/javascript"></script>
</body>
</html>