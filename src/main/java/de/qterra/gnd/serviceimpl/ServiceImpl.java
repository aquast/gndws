package de.qterra.gnd.serviceimpl;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;


import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.services.GndRequesterSkeletonInterface;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.OAContentByPersonRequest;
import de.qterra.gnd.sparql.requests.OpenLibContentByPersonRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;
import de.qterra.gnd.webservice.GetGndKeywordResponse;
import de.qterra.gnd.webservice.GetGndPersonInfo;
import de.qterra.gnd.webservice.GetGndPersonInfoResponse;
import de.qterra.gnd.webservice.GetPublicationsByCreatorName;
import de.qterra.gnd.webservice.GetPublicationsByCreatorNameResponse;
import de.qterra.gnd.webservice.KeywordResultType;
import de.qterra.gnd.webservice.PublResultType;
import de.qterra.gnd.webservice.ResultType;

public class ServiceImpl implements GndRequesterSkeletonInterface {
	private ArrayList<Hashtable<String,RDFNode>> results = new ArrayList<Hashtable<String,RDFNode>>();
	private String firstName = "";
	private String lastName = "";

	@Override
	public GetGndPersonInfoResponse getGndPersonInfo(
			GetGndPersonInfo getGndPersonInfo) {

		GetGndPersonInfoResponse response = new GetGndPersonInfoResponse();
		firstName = getGndPersonInfo.getFirstName();
		lastName = getGndPersonInfo.getLastName();
		
		PersonRequest persReq = new PersonRequest();
		ArrayList<Hashtable<String,RDFNode>> results = persReq.performRequest(firstName, lastName);
		ArrayList<ResultType> resultArray = new ArrayList<ResultType>();
		
		response.setResultSize(results.size());
		
		for (int i=0; i<results.size(); i++){
			Hashtable<String,RDFNode> resLine = results.get(i);
			
			ResultType res = new ResultType();
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
		
		ResultType[] resType = null;
		resultArray.toArray(resType = new ResultType[resultArray.size()] );
		response.setResult(resType) ;
		return response;
	}


	@Override
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


	@Override
	public GetPublicationsByCreatorNameResponse getPublicationsByCreatorName(
			GetPublicationsByCreatorName getPublicationsByCreatorName) {
		
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

		}
			
		
		// TODO Auto-generated method stub
		return null;
	}

	public class OARunable implements Runnable {

		@Override
		public void run() {
			//Test OA Explorer
			OAContentByPersonRequest oaReq = new OAContentByPersonRequest();
			ArrayList<Hashtable<String,RDFNode>> oaResults = oaReq.performRequest(firstName , lastName);
			results.addAll(oaResults);
			
			
		}
		
	}
	public class OlRunable implements Runnable {

		@Override
		public void run() {

			//Test OpenLibrary 
		    OpenLibContentByPersonRequest olReq = new OpenLibContentByPersonRequest();
		    ArrayList<Hashtable<String,RDFNode>> olResults = olReq.performRequest(firstName , lastName);
			results.addAll(olResults);
			
			
		}
		
	}

}
