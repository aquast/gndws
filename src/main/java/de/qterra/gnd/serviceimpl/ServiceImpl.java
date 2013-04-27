package de.qterra.gnd.serviceimpl;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Properties;

import org.apache.log4j.Logger;


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
import de.qterra.gnd.sparql.util.UnifyResults;
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

public class ServiceImpl implements GndRequesterSkeletonInterface {

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(ServiceImpl.class);

	private ArrayList<Hashtable<String,RDFNode>> results = null;
	
	// these Strings are not null, in order to avoid a null pointer exception <- nessecary???  
	private String firstName = null;
	private String lastName = null;
	private String pnd = null;
	private String issn = null;

	@Override
	public GetGndPersonInfoResponse getGndPersonInfo(
			GetGndPersonInfo getGndPersonInfo) {

		GetGndPersonInfoResponse response = new GetGndPersonInfoResponse();
		ArrayList<Properties> propertyList = new ArrayList<Properties>();
		results = new ArrayList<Hashtable<String,RDFNode>>();
		
		firstName = getGndPersonInfo.getFirstName();
		lastName =  getGndPersonInfo.getLastName();
		
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


	//@Override
	public de.qterra.gnd.webservice.GetGndKeywordResponse getGndKeyword(
			de.qterra.gnd.webservice.GetGndKeyword getGndKeyword) {
		// TODO Auto-generated method stub
		GetGndKeywordResponse response = new GetGndKeywordResponse();
		String keyword = getGndKeyword.getKeyword();
		
		ClassificationRequest classReq = new ClassificationRequest();
		ArrayList<Hashtable<String,RDFNode>> results = classReq.performRequest(keyword);
		ArrayList<KeywordResultType> resultArray = new ArrayList<KeywordResultType>();
		
		response.setResultSize(results.size());
		
		for (int i=0 ; i< results.size() ; i++){
		    KeywordResultType keyResult = new KeywordResultType();
	    	Hashtable<String,RDFNode> soln = results.get(i); 
	    	
	    	System.out.print(soln.get("uri") + ": ");
	    	System.out.print(soln.get("label"));
	    	if(soln.containsKey("nLabel")){
		    	System.out.println(": engerer Begriff = " + soln.get("nLabel"));
		    }
	    	else{
	    		System.out.println("");
	    	}
	    }
	    KeywordResultType[] keywRes = null;
	    response.setResult(keywRes);
	    return response;
	}


	//@Override
	public GetPublicationsByCreatorNameResponse getPublicationsByCreatorName(
			GetPublicationsByCreatorName getPublicationsByCreatorName) {
		/*
		GetPublicationsByCreatorNameResponse response = new GetPublicationsByCreatorNameResponse();
		firstName = getPublicationsByCreatorName.getFirstName();
		lastName = getPublicationsByCreatorName.getLastName();

		Thread sparqlOAThread = new Thread(new OARunable());
		sparqlOAThread.setName("OAExpl. Thread");
		sparqlOAThread.start();
		
		Thread sparqlOlThread = new Thread(new OlRunable());
		sparqlOlThread.setName("OpenLibrary Thread");
		sparqlOlThread.start();
		
		
		try {
			sparqlOlThread.join();
			sparqlOAThread.join();
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		response.setResultSize(results.size());
		
		for (int i=0; i<results.size(); i++){
			Hashtable<String,RDFNode> resLine = results.get(i);
			
			PublResultType res = new PublResultType();
			res.setPublTitle(resLine.get("ptitle").toString());

			if(resLine.containsKey("urn")){
				res.setUrn(resLine.get("urn").toString());
			}
			
			if(resLine.containsKey("page")){
				res.addPage(resLine.get("page").toString());
			}
			
			if(resLine.containsKey("publication")){
				res.addTestemonial(resLine.get("publication").toString());
			}

		}*/
			
		
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public GetResourcesByPndResponse getResourcesByPnd(
			GetResourcesByPnd getResourcesByPnd) {
		
		GetResourcesByPndResponse response = new GetResourcesByPndResponse();
		ArrayList<Properties> propertyList = new ArrayList<Properties>();
		results = new ArrayList<Hashtable<String,RDFNode>>();
		
		pnd = getResourcesByPnd.getPnd();
		
		Properties b3katPersResReqProp = new Properties();
		b3katPersResReqProp.setProperty("requestUrl", "http://lod.b3kat.de/sparql");
		b3katPersResReqProp.setProperty("sparqlFile", "b3katPersonResourcesRequest.txt");
		b3katPersResReqProp.setProperty("$pnd", "<http://d-nb.info/gnd/" + pnd + ">");
		propertyList.add(b3katPersResReqProp);

		Properties gndPersResReqProp = new Properties();
		gndPersResReqProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		gndPersResReqProp.setProperty("sparqlFile", "gndResourcesRequest.txt");
		gndPersResReqProp.setProperty("$pnd", "<http://d-nb.info/gnd/" + pnd + ">");
		propertyList.add(gndPersResReqProp);


		runRequests(propertyList);

		ArrayList<ResourceResultType> resultArray = new ArrayList<ResourceResultType>();
		
		// create appropriate GndPersonInfoResponse from results arraylist 
		response.setResultSize(results.size());
		
		for (int i=0; i<results.size(); i++){
			Hashtable<String,RDFNode> resLine = results.get(i);
			
			ResourceResultType res = new ResourceResultType();
			res.addPndUri("<http://d-nb.info/gnd/" + pnd + ">");
			res.setResourceUri(resLine.get("uri").toString());
			//res.setResourceTitle(resLine.get("title").toString());
			
			if(resLine.containsKey("title")){
				res.setResourceTitle(resLine.get("title").toString());
			}
			if(resLine.containsKey("isbn")){
				res.addIsbn(resLine.get("isbn").toString());
			}
			if(resLine.containsKey("issn")){
				res.setIssn(resLine.get("issn").toString());
			}
			if(resLine.containsKey("extent")){
				res.setExtent(resLine.get("extent").toString());
			}
			if(resLine.containsKey("publisher")){
				res.setPublisher(resLine.get("publisher").toString());
			}
			if(resLine.containsKey("issued")){
				res.setIssued(resLine.get("issued").toString().substring(0, 4));
			}
			if(resLine.containsKey("name")){
				res.setIssued(resLine.get("name").toString().substring(0, 4));
			}

			resultArray.add(res);
		}
		
		ResourceResultType[] resType = null;
		resultArray.toArray(resType = new ResourceResultType[resultArray.size()] );
		response.setResult(resType) ;
		return response;
	}

	@Override
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

		ArrayList<ResourceResultType> resultArray = new ArrayList<ResourceResultType>();

		
		// create appropriate GndPersonInfoResponse from results arraylist 
		response.setResultSize(results.size());
		
		for (int i=0; i<results.size(); i++){
			Hashtable<String,RDFNode> resLine = results.get(i);
			
			ResourceResultType res = new ResourceResultType();
			res.setResourceUri(resLine.get("uri").toString());
			
			if(resLine.containsKey("person")){
				res.addPndUri(resLine.get("person").toString());
			}
			if(resLine.containsKey("name")){
				res.addPrefferedName(resLine.get("name").toString());
			}
			if(resLine.containsKey("title")){
				res.setResourceTitle(resLine.get("title").toString());
			}
			if(resLine.containsKey("isbn")){
				res.addIsbn(resLine.get("isbn").toString());
			}
			if(resLine.containsKey("issn")){
				res.setIssn(resLine.get("issn").toString());
			}
			if(resLine.containsKey("extent")){
				res.setExtent(resLine.get("extent").toString());
			}
			if(resLine.containsKey("publisher")){
				res.setPublisher(resLine.get("publisher").toString());
			}
			if(resLine.containsKey("issued")){
				res.setIssued(resLine.get("issued").toString().substring(0, 4));
			}

			resultArray.add(res);
		}
		
		
		ResourceResultType[] resType = null;
		resultArray.toArray(resType = new ResourceResultType[resultArray.size()] );
		response.setResult(resType) ;
		
		
		return response;
	}

	
	/**
	 * <p><em>Title: Perform the request previously created</em></p>
	 * <p>Description: Mathos takes a List of Requests and delegates them 
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
