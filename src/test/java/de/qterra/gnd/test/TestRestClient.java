/**
 * TestRestClient.java - This file is part of the DiPP Project by hbz
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
package de.qterra.gnd.test;

import org.apache.log4j.Logger;
import org.junit.Test;

import java.net.URI;

import javax.ws.rs.core.MediaType;
import javax.ws.rs.core.UriBuilder;

import com.sun.jersey.api.client.Client;
import com.sun.jersey.api.client.ClientResponse;
import com.sun.jersey.api.client.WebResource;
import com.sun.jersey.api.client.config.ClientConfig;
import com.sun.jersey.api.client.config.DefaultClientConfig;

import de.qterra.gnd.util.Configuration;

/**
 * Class TestRestClient
 * 
 * <p><em>Title: </em></p>
 * <p>Description: </p>
 * 
 * @author aquast, email
 * creation date: 29.07.2013
 *
 */
public class TestRestClient {

	// Initiate Logger for TestRestClient
	private static Logger log = Logger.getLogger(TestRestClient.class);
	
	//private String uri = Configuration.getServiceUrl();
	private String uri = "http://localhost:8080";
	
	@Test public void callRestFulServiceXml(){
		Client client = createClient();
		WebResource wResource = client.resource(uri + "/loddiggr/api/personInfo");
		wResource = wResource.queryParam("firstName", "Andres")
			.queryParam("lastName", "Quast");
		
		log.info(wResource);
		log.info(wResource.post(String.class).toString());
		
		
		
	}
	

	private Client createClient(){
		ClientConfig config = new DefaultClientConfig();
		Client client = Client.create(config);
		return client;
	}
	
	/**
	 * <p><em>Title: </em></p>
	 * <p>Description: </p>
	 * 
	 * @param args 
	 */
	public static void main(String[] args) {
		Configuration.initLog();
		TestRestClient trClient= new TestRestClient();
		trClient.callRestFulServiceXml();
		//trClient.callConvertFromUrlServiceJson();
		//trClient.callRestFulServiceJson();

	}
}
