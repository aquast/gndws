package de.qterra.gnd.serviceimpl;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.Properties;

import org.apache.log4j.Logger;

import javax.ws.rs.DefaultValue;
import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.QueryParam;
import javax.ws.rs.core.MediaType;

import com.hp.hpl.jena.rdf.model.RDFNode;
import com.sun.jersey.api.json.JSONWithPadding;

import de.qterra.gnd.sparql.requests.GenericSPARQLRequest;
import de.qterra.gnd.util.PersonResult;
import de.qterra.gnd.util.PersonResultList;

@Path("/personInfo")
public class GetGndPersonInfoService{

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(GetGndPersonInfoService.class);

	private ArrayList<Hashtable<String,RDFNode>> results = null;
	
	/*
	@GET
	@Produces({MediaType.APPLICATION_XML})
	public PersonResultList getGndPersonInfo(@QueryParam("firstName") String firstName, 
			@QueryParam("lastName") String lastName,
			@QueryParam("index") int index) {

		return gndPersonInfo(firstName, lastName);
	}
	*/

	@GET
	@Produces({"application/x-javascript", MediaType.APPLICATION_JSON})
	public JSONWithPadding getGndPersonInfoJsonP(
			@QueryParam("callback") @DefaultValue("fn") String callback,
			@QueryParam("firstName") String firstName, 
			@QueryParam("lastName") String lastName,
			@QueryParam("index") int index) {

		return new JSONWithPadding(gndPersonInfo(firstName, lastName), callback);
	}

	@GET
	@Produces({MediaType.APPLICATION_XML})
	public PersonResultList getGndPersonInfo(@QueryParam("firstName") String firstName, 
			@QueryParam("lastName") String lastName,
			@QueryParam("index") int index) {

		return gndPersonInfo(firstName, lastName);
	}

	@POST
	@Produces({MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON})
	public PersonResultList postGndPersonInfo(@QueryParam("firstName") String firstName, 
			@QueryParam("lastName") String lastName,
			@QueryParam("index") int index) {

		return gndPersonInfo(firstName, lastName);
	}

	
	public PersonResultList gndPersonInfo(String firstName, String lastName) {
		
		
		//create Props for runRequests
		ArrayList<Properties> propertyList = new ArrayList<Properties>();
		results = new ArrayList<Hashtable<String,RDFNode>>();

		if(firstName != null && lastName !=null){
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
			
		}

		runRequests(propertyList);

		//Parse requestResult
		PersonResultList resultList = new PersonResultList();

		resultList.setResultSize(Integer.toString(results.size()));
		
		ArrayList<PersonResult> prList = resultList.getPersonResultList();
		
		for (int i=0; i<results.size(); i++){
			PersonResult pResult = new PersonResult();
			
			Hashtable<String,RDFNode> resLine = results.get(i);
			
			pResult.setPreferredName(resLine.get("name").toString());
			pResult.setPersIdentUri(resLine.get("uri").toString());
			pResult.setPersIdent(resLine.get("uri").toString().substring(21));
			
			
			if(resLine.containsKey("birth")){
				pResult.setBirth(resLine.get("birth").toString());
			}
			if(resLine.containsKey("biogr")){
				pResult.setBiogr(resLine.get("biogr").toString());
			}
			if(resLine.containsKey("acad")){
				pResult.setAcademicTitle(resLine.get("acad").toString());
			}
			if(resLine.containsKey("link")){
				pResult.setWpUrl(resLine.get("wpUrl").toString());
			}
			
			prList.add(pResult);
		}

		return resultList;
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
