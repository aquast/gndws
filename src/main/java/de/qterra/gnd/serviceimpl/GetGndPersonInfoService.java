package de.qterra.gnd.serviceimpl;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Properties;

import org.apache.log4j.Logger;

import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.QueryParam;
import javax.ws.rs.core.MediaType;


import com.hp.hpl.jena.rdf.model.RDFNode;
import java.text.Normalizer;

import de.qterra.gnd.services.GetResourcesByIdentifier;
import de.qterra.gnd.services.GetResourcesByIdentifierResponse;
import de.qterra.gnd.services.GetRessourcesByIdentifier;
import de.qterra.gnd.services.GetRessourcesByIdentifierResponse;
import de.qterra.gnd.services.GndRequesterSkeletonInterface;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.GenericSPARQLRequest;
import de.qterra.gnd.sparql.requests.OAContentByPersonRequest;
import de.qterra.gnd.sparql.requests.OpenLibContentByPersonRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;
import de.qterra.gnd.sparql.util.ResourceResponse;
import de.qterra.gnd.sparql.util.UnifyResults;
import de.qterra.gnd.util.PersonResult;
import de.qterra.gnd.webservice.GetGndKeywordResponse;
import de.qterra.gnd.webservice.GetGndPersonInfo;
import de.qterra.gnd.webservice.GetGndPersonInfoResponse;
import de.qterra.gnd.webservice.GetPublicationsByCreatorName;
import de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse;
import de.qterra.gnd.webservice.GetResourcesByPnd;
import de.qterra.gnd.webservice.GetResourcesByPndResponse;
import de.qterra.gnd.webservice.KeywordResultType;
import de.qterra.gnd.webservice.PersonResultType;
import de.qterra.gnd.webservice.PublResultType;
import de.qterra.gnd.webservice.ResourceResultType;
import de.qterra.gnd.webservice.ResultType;

public class GetGndPersonInfoService{

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(GetGndPersonInfoService.class);

	private ArrayList<Hashtable<String,RDFNode>> results = null;
	
	// these Strings are not null, in order to avoid a null pointer exception <- nessecary???  
	private String firstName = null;
	private String lastName = null;
	private String pnd = null;
	private String issn = null;

	@Path("/personInfo")
	@POST
	@Produces({MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON})
	public PersonResultList getGndPersonInfo(@QueryParam("firstName") String firstName, 
			@QueryParam("lastName") String lastName,
			@QueryParam("index") int index) {

		PersonResult pResult = new PersonResult();
		//GetGndPersonInfoResponse response = new GetGndPersonInfoResponse();
		ArrayList<Properties> propertyList = new ArrayList<Properties>();
		results = new ArrayList<Hashtable<String,RDFNode>>();
		
		Properties persReqProp = new Properties();
		persReqProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		persReqProp.setProperty("sparqlFile", "gndPersonRequest.txt");
		persReqProp.setProperty("$firstName", firstName);
		persReqProp.setProperty("$lastName", lastName);
		propertyList.add(persReqProp);

		Properties persExtendedReqProp = new Properties();
		persExtendedReqProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		persExtendedReqProp.setProperty("sparqlFile", "gndExtendedPersonRequest.txt");
		persExtendedReqProp.setProperty("$firstName", firstName);
		persExtendedReqProp.setProperty("$lastName", lastName);
		propertyList.add(persExtendedReqProp);


		runRequests(propertyList);

		ArrayList<PersonResultType> resultArray = new ArrayList<PersonResultType>();
		
		// create appropriate GndPersonInfoResponse from results arraylist 
		response.setResultSize(results.size());
		
		for (int i=0; i<results.size(); i++){
			Hashtable<String,RDFNode> resLine = results.get(i);
			
			PersonResultType res = new PersonResultType();
			res.setPndUri(resLine.get("uri").toString());
			res.setPrefferedName(resLine.get("name").toString());
			res.setPndID(resLine.get("uri").toString().substring(21));
			
			
			if(resLine.containsKey("birth")){
				res.setYearOfBirth(resLine.get("birth").toString());
			}
			if(resLine.containsKey("link")){
				res.addWpUrl(resLine.get("link").toString());
			}
			if(resLine.containsKey("biogr")){
				res.setBiograficData(resLine.get("biogr").toString());
			}
			if(resLine.containsKey("acad")){
				res.setAcadTitle(resLine.get("acad").toString());
			}
			if(resLine.containsKey("link")){
				res.addWpUrl(resLine.get("wpUrl").toString());
			}
			
			resultArray.add(res);
		}
		
		PersonResultType[] resType = null;
		resultArray.toArray(resType = new PersonResultType[resultArray.size()] );
		response.setResult(resType) ;
		return response;
	}



	
	/**
	 * <p><em>Title: Perform the request previously created</em></p>
	 * <p>Description: Method takes a List of Requests and delegates them 
	 * to single Request Threads</p>
	 *  
	 * @param propertyList 
	 */
	private void runRequests(ArrayList<Properties> propertyList){

		ArrayList<Thread> threadList = new ArrayList<Thread>();
		for(int i=0; i < propertyList.size(); i++){
			Properties reqProp = propertyList.get(i);
			SparqlRunnable spRun = new SparqlRunnable();
			spRun.setProperties(reqProp);
   			Thread sparqlThread = new Thread(spRun);
			sparqlThread.setName("GenericSparqlThread_" + i);
			sparqlThread.start();
			threadList.add(sparqlThread);
			 
		}
		
		
		// TODO: implement work flow that catches Threads if a single Thread fails
		for(int i=0; i < threadList.size(); i++){
			try {
				threadList.get(i).join();
				log.info(threadList.get(i).getName());
			}catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		
	}

	public class SparqlRunnable implements Runnable {

		private Properties reqProp = null;
		
		@Override
		public void run() {
			GenericSPARQLRequest gsReq = new GenericSPARQLRequest(reqProp);
			ArrayList<Hashtable<String,RDFNode>> sparqlResults = gsReq.performRequest();
			results.addAll(sparqlResults);
			
			
		}
		
		public void setProperties(Properties ReqProp){
			reqProp = ReqProp;
		}
		
	}




}
