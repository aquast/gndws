/**
 * TestGndRequesterAsClient.java - This file is part of the DiPP Project by hbz
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

import java.rmi.RemoteException;

import org.apache.axis2.AxisFault;
import org.apache.axis2.databinding.types.URI;
import org.apache.axis2.databinding.types.URI.MalformedURIException;
import org.apache.log4j.Logger;

import com.sun.org.apache.xalan.internal.xsltc.runtime.Parameter;

import de.qterra.gnd.client.GndRequesterStub;
import de.qterra.gnd.client.GndRequesterStub.GetGndPersonInfo;
import de.qterra.gnd.client.GndRequesterStub.GetGndPersonInfoResponse;
import de.qterra.gnd.client.GndRequesterStub.GetPublicationsByCreatorName;
import de.qterra.gnd.client.GndRequesterStub.GetPublicationsByCreatorNameResponse;
import de.qterra.gnd.client.GndRequesterStub.PublResultType;
import de.qterra.gnd.client.GndRequesterStub.ResultType;
import de.qterra.gnd.util.Configuration;

/**
 * Class TestGndRequesterAsClient
 * 
 * <p><em>Title: </em></p>
 * <p>Description: Test the XsltTransformer WebService as Client</p>
 * 
 * @author aquast, email
 * creation date: 18.06.2010
 *
 */
public class TestSparQLEndpointRequestAsClient {

	/**
	 * Default Constructor for getting Configuration and set up logging
	 */
	public TestSparQLEndpointRequestAsClient(){
		//Configuration.initLog();
	}

	// Initiate Logger for TestGndRequesterAsClient
	private static Logger log = Logger.getLogger(TestSparQLEndpointRequestAsClient.class);

	public void sparqlRequest() {
		GndRequesterStub stub = null;
		
		GetGndPersonInfoResponse gndResponse = new GetGndPersonInfoResponse();
		
		try {
			stub = new GndRequesterStub("http://localhost:8080/axis2/services/gndRequester");
			//stub = new GndRequesterStub("http://melpomene.hbz-nrw.de:9180/axis2/services/gndRequester");
			//stub = new GndRequesterStub("http://phacops.dyndns.org:8080/axis2/services/gndRequester");
			//stub = new GndRequesterStub("http://192.168.1.39:8080/axis2/services/gndRequester");
		} catch (AxisFault e) {
			log.error(e);
			e.printStackTrace();
		}
		
		de.qterra.gnd.client.GndRequesterStub.GetGndPersonInfo gnd = new GetGndPersonInfo();
		gnd.setFirstName("Carl Friedrich");
		gnd.setLastName("Gauß");
		
		try {
			gndResponse = stub.getGndPersonInfo(gnd);
		} catch (RemoteException e) {
			log.error(e);
			e.printStackTrace();
		}
		ResultType[] res = gndResponse.getResult(); 
		if(res != null){
			log.info("returned " + res.length + " results");
			log.info("Example: " + res[0].getPndID());
			log.info("Example: " + res[0].getPrefferedName());
			log.info("Example: " + res[0].getYearOfBirth());
		}
		else{
			log.info("no Results returned");
		}
			
		
	}
	
	/**
	 * <p><em>Title: </em></p>
	 * <p>Description: </p>
	 * 
	 * @param args 
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		TestSparQLEndpointRequestAsClient client = new TestSparQLEndpointRequestAsClient();
		client.sparqlRequest();

	}
}
