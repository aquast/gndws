package de.qterra.gnd.serviceimpl;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.Properties;

import org.apache.log4j.Logger;

import javax.ws.rs.GET;
import javax.ws.rs.POST;
import javax.ws.rs.Path;
import javax.ws.rs.Produces;
import javax.ws.rs.QueryParam;
import javax.ws.rs.core.MediaType;

import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.services.GetResourcesByIdentifier;
import de.qterra.gnd.services.GetResourcesByIdentifierResponse;
import de.qterra.gnd.sparql.requests.GenericSPARQLRequest;
import de.qterra.gnd.sparql.util.ResourceResponse;
import de.qterra.gnd.sparql.util.UnifyResults;
import de.qterra.gnd.util.PersonResult;
import de.qterra.gnd.util.PersonResultList;
import de.qterra.gnd.webservice.ResourceResultType;

@Path("/resourcesInfo")
public class GetRessourcesByGndIDService{

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(GetRessourcesByGndIDService.class);

	private ArrayList<Hashtable<String,RDFNode>> results = null;
	
	@GET
	@Produces({MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON})
	public PersonResultList getResourcesByIdInfo(@QueryParam("firstName") String firstName, 
			@QueryParam("lastName") String lastName,
			@QueryParam("index") int index) {

		return gndPersonInfo(firstName, lastName);
	}
		
	@POST
	@Produces({MediaType.APPLICATION_XML, MediaType.APPLICATION_JSON})
	public PersonResultList postResourcesByIdInfo(@QueryParam("firstName") String firstName, 
			@QueryParam("lastName") String lastName,
			@QueryParam("index") int index) {

		return gndPersonInfo(firstName, lastName);
	}

	public GetResourcesByIdentifierResponse getResourcesByIdentifier(
			GetResourcesByIdentifier getResourcesByIdentifier) {
		GetResourcesByIdentifierResponse response = new GetResourcesByIdentifierResponse();

		ArrayList<Properties> propertyList = new ArrayList<Properties>();
		results = new ArrayList<Hashtable<String,RDFNode>>();
		
		String isbn = null;
		int isbnType = 0;
		String idType = getResourcesByIdentifier.getIdType();
		String id = getResourcesByIdentifier.getIdString();
				
		if(idType.equals("isbn")){
			isbn = id.replace("-", "");
			isbnType = isbn.length();
			log.info(isbnType);
		}
		
		Properties gndResByIsbnProp = new Properties();
		gndResByIsbnProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		gndResByIsbnProp.setProperty("sparqlFile", "gndResourcesByIsbn" + isbnType + "Request.txt");
		gndResByIsbnProp.setProperty("$isbn", isbn);
		propertyList.add(gndResByIsbnProp);
		
		runRequests(propertyList);

	    // unify those rows from the SPARQL Response, that cohave the same Resource ID   
		UnifyResults uni = new UnifyResults();
	    uni.setResults(results);
	    //ArrayList<Hashtable<String,ArrayList<String>>> unifiedResults = uni.unify();
	    ArrayList<Hashtable<String, ResourceResponse>> unifiedResults 
	    = uni.unify("uri");

		
		
		ArrayList<ResourceResultType> resultArray = new ArrayList<ResourceResultType>();

		
		// create appropriate GndPersonInfoResponse from results arraylist 
		response.setResultSize(unifiedResults.size());
		
		for (int i=0; i<unifiedResults.size(); i++){

			ResourceResultType res = new ResourceResultType();
	    	Hashtable<String, ResourceResponse> uResult = unifiedResults.get(i); 

	    	//log.info(unifiedResults.size());
	    	Enumeration<String> kEnum = uResult.keys();
    		int k = 1;
	    	while (kEnum.hasMoreElements()){
	    		String rKey = kEnum.nextElement();
				res.setResourceUri(rKey);
	    		Enumeration<String> keyEnum =  uResult.get(rKey).getResponse().keys();
	    		while(keyEnum.hasMoreElements()){
	    			String key = keyEnum.nextElement();
	    			ArrayList<String> value = uResult.get(rKey).getResponse().get(key);
	    			for(int j = 0; j<value.size(); j++){
		    			if(key.equals("person")){
		    				res.addPndUri(value.get(j));
		    			}
		    			
		    			if(key.equals("name")){
		    				res.addPrefferedName(value.get(j));
		    			}
		    			
		    			if(key.equals("title")){
		    				res.setResourceTitle(value.get(j));
		    			}
		    			
		    			if(key.equals("isbn")){
		    				res.addIsbn(value.get(j));
		    			}
		    			
		    			if(key.equals("extent")){
		    				res.addExtent(value.get(j));
		    				//TODO: make this multiple
		    			}
		    			
		    			if(key.equals("publisher")){
		    				res.setPublisher(value.get(j));
		    			}
		    			
		    			if(key.equals("issued")){
		    				res.setIssued(value.get(j));
		    			}
		    			
	    			}

	    		}
	    	}			

			resultArray.add(res);
		}
		
		
		ResourceResultType[] resType = null;
		resultArray.toArray(resType = new ResourceResultType[resultArray.size()] );
		response.setResult(resType) ;
		
		
		return response;
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
