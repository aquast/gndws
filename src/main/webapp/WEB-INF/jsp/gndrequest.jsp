<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet href="gndPerson.xslt" type="text/xsl"?>
<%@ page language="java" contentType="text/xml; charset=UTF-8"
    pageEncoding="UTF-8"%>
<%@ page import="org.xml.sax.*" %>
<%@ page import="javax.xml.transform.*" %>
<%@ page import="java.io.*" %>
<%@ page import="java.net.*" %>
<%
response.addHeader("Access-Control-Allow-Origin", "http://alkyoneus.hbz-nrw.de/dev");
try {
	// this jsp template provides acces to the rest sercive of a remote gnd triplestore
	// it adds a processing instruction to the rest output required for xsl-transformtion
	// and if installed on the same server where the caller html form resists solves 
	// the cross domain problem associated wih xslt and js (hopefully)
	// Please edit the urls to your requirements
	String url = "http://phacops.dyndns.org:8080/axis2/services/gndRequester/getGndPersonInfo?";

	String fName = URLEncoder.encode(request.getParameter("firstName"), "UTF-8");
	String lName = URLEncoder.encode(request.getParameter("lastName"), "UTF-8");
	//String fName = URI.create(request.getParameter("firstName")).toString();
	//String lName = URI.create(request.getParameter("lastName")).toString();
	String requestUrl = url + "firstName=" + fName.replace("+", "%20") + "&lastName=" + lName;
	
	//URI rUri = URI.create(requestUrl);
	//URL rUrl = rUri.toURL();
	URL rUrl = new URL(requestUrl);

	InputStream is = rUrl.openStream();
	BufferedInputStream bis = new BufferedInputStream(is);

	//FileOutputStream fos = new FileOutputStream(requestUrl);
	//BufferedOutputStream bos = new BufferedOutputStream(fos);

	
	ByteArrayOutputStream bos = new ByteArrayOutputStream();
	int i = -1;
	while((i = bis.read()) != -1){
		bos.write(i);
	}
	
    out.print(bos);
    }
  catch (Exception e) {
    e.printStackTrace( );
    }
%>
