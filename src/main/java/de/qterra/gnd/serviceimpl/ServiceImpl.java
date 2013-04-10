package de.qterra.gnd.serviceimpl;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Properties;

import org.apache.log4j.Logger;


import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.services.GndRequesterSkeletonInterface;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.GenericSPARQLRequest;
import de.qterra.gnd.sparql.requests.OAContentByPersonRequest;
import de.qterra.gnd.sparql.requests.OpenLibContentByPersonRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;
import de.qterra.gnd.test.TestGenericSparqlRequest;
import de.qterra.gnd.test.TestGenericSparqlRequest.SparqlRunnable;
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
import de.qterra.gnd.webservice.ResultType;

public class ServiceImpl implements GndRequesterSkeletonInterface {

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(ServiceImpl.class);

	private ArrayList<Properties> propertyList = new ArrayList<Properties>();
	private ArrayList<Hashtable<String,RDFNode>> results = new ArrayList<Hashtable<String,RDFNode>>();
	
	// these Strings are not null, in order to avoid a null pointer exception <- nessecary???  
	private String firstName = null;
	private String lastName = null;
	private String pndUri = null;
	private String issn = null;

	@Override
	public GetGndPersonInfoResponse getGndPersonInfo(
			GetGndPersonInfo getGndPersonInfo) {

		GetGndPersonInfoResponse response = new GetGndPersonInfoResponse();
		
		firstName = getGndPersonInfo.getFirstName();
		lastName = getGndPersonInfo.getLastName();
		
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

		// create request threads
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
		
		
		
		for(int i=0; i < threadList.size(); i++){
			try {
				threadList.get(i).join();
				log.info(threadList.get(i).getName());
			}catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}

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


	@Override
	public GetResourcesByPndResponse getResourcesByPnd(
			GetResourcesByPnd getResourcesByPnd) {
		// TODO Auto-generated method stub
		return null;
	}

}
