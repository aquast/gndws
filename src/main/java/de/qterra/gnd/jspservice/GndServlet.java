/**
 * GndServlet.java - This file is part of the DiPP Project by hbz
 * Library Service Center North Rhine Westfalia, Cologne 
 *
 * -----------------------------------------------------------------------------
 *
 * <p><b>License and Copyright: </b>The contents of this file are subject to the
 * D-FSL License Version 1.0 (the "License"); you may not use this file
 * except in compliance with the License. You may obtain a copy of the License
 * at <a href="http://www.dipp.nrw.de/dfsl/">http://www.dipp.nrw.de/dfsl/.</a></p>
 *
 * <p>Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.</p>
 *
 * <p>Portions created for the Fedora Repository System are Copyright &copy; 2002-2005
 * by The Rector and Visitors of the University of Virginia and Cornell
 * University. All rights reserved."</p>
 *
 * -----------------------------------------------------------------------------
 *
 */
package de.qterra.gnd.jspservice;

import javax.servlet.*;
import javax.servlet.http.*;
import javax.servlet.jsp.JspWriter;

import java.io.*;
import javax.xml.transform.*;


import org.apache.log4j.Logger;

/**
 * Class GndServlet
 * 
 * <p><em>Title: Servlet is not in use!!!</em></p>
 * <p>Description: </p>
 * 
 * @author aquast, email
 * creation date: 26.11.2010
 *
 */
public class GndServlet extends HttpServlet {

	// Initiate Logger for GndServlet
	private static Logger log = Logger.getLogger(GndServlet.class);
	private String stylesheet = "http://phacops.dyndns.org:8080/test_gnd.xslt";
	private String uri = "http://phacops.dyndns.org:8080/axis2/services/gndRequester/getGndPersonInfo?";
	
	public BufferedOutputStream applyXslt(String xmlRequestUri){
		BufferedOutputStream bos = null;
		try {
		TransformerFactory tFactory = TransformerFactory.newInstance();
	    Transformer transformer =
	    tFactory.newTransformer(new javax.xml.transform.stream.StreamSource(stylesheet));
		    transformer.transform
		      (new javax.xml.transform.stream.StreamSource(xmlRequestUri),
		       new javax.xml.transform.stream.StreamResult
		            (bos));
		  }
		  catch (Exception e) {
		    log.error(e);
		  }
		return bos;
		}
	


	public void doGet(HttpServletRequest request, HttpServletResponse response)
    throws IOException, ServletException{
		String fName = request.getParameter("firstName");
		String lName = request.getParameter("lastName");
		String requestUri = uri + "firstName=" + fName + "&amp;lastName=" + lName;
		PrintWriter out = response.getWriter();
		out.print(applyXslt(requestUri));
	}
}
