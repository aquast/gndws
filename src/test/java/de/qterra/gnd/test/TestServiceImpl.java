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

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.junit.Test;

import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.serviceimpl.ServiceImpl;
import de.qterra.gnd.webservice.GetGndPersonInfo;
import de.qterra.gnd.webservice.GetGndPersonInfoResponse;
import de.qterra.gnd.webservice.PersonResultType;
import de.qterra.gnd.webservice.GetResourcesByPnd;
import de.qterra.gnd.webservice.GetResourcesByPndResponse;
import de.qterra.gnd.webservice.ResourceResultType;

//import de.qterra.gnd.webservice.ResultType;

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
public class TestServiceImpl {

	// Initiate Logger for TestGndRequesterAsClient
	private static Logger log = Logger.getLogger(TestServiceImpl.class);

	@Test public void testGndPersonInfo() {
		
		ServiceImpl sImpl= new ServiceImpl();
		
		GetGndPersonInfo persInfo = new GetGndPersonInfo();
		persInfo.setFirstName("Loki");
		persInfo.setLastName("Schmidt");
		GetGndPersonInfoResponse response = sImpl.getGndPersonInfo(persInfo);
		
		
		PersonResultType[] res = response.getResult(); 


		if(res != null){
			for(int i = 0 ; i < res.length; i++){
				log.info(res[i].getPrefferedName() 
						 + " ; " + res[i].getPndUri()
						 + " ; " + res[i].getYearOfBirth()
						 + " ; " + res[i].getAcadTitle());
			}
			// ask for resources
			for(int i = 0 ; i < res.length; i++){
				//Request ServiceImpl
				GetResourcesByPnd resInfo = new GetResourcesByPnd();
				resInfo.setPndUri(res[i].getPndUri());
				GetResourcesByPndResponse response2 = sImpl.getResourcesByPnd(resInfo);
				ResourceResultType[] rResult = response2.getResult();
				
				
				log.info("Anzahl gefundene Resourcen von " 
						+ res[i].getPrefferedName() + ", " + res[i].getPndUri() + ": " + response2.getResultSize());
				for(int j = 0; j < rResult.length; j++){
					log.info(res[i].getPrefferedName() + " " 
					+  "(" + rResult[j].getIssued() + "): " 
						+ rResult[j].getResourceTitle() + ". - " 
						+  rResult[j].getExtent() + ", " 
						+  rResult[j].getPublisher() +  ", " 
						+ rResult[j].getIsbn() +  ", " 
						//+ rResult[j].getIssn() +  ", " 
						+ ". - URI: " + rResult[j].getResourceUri());
				}
			}
		}else{
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
		TestServiceImpl servImp = new TestServiceImpl();
		servImp.testGndPersonInfo();

	}
}
