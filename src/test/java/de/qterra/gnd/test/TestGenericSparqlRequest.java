/**
 * 
 */
package de.qterra.gnd.test;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Properties;

import com.hp.hpl.jena.rdf.model.RDFNode;

import org.junit.Test;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.GenericSPARQLRequest;
import de.qterra.gnd.sparql.requests.OAContentByPersonRequest;
import de.qterra.gnd.sparql.requests.OpenLibContentByPersonRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;

/**
 * @author aquast
 *
 */
public class TestGenericSparqlRequest{

	/**
	 * @param args
	 */
	public ArrayList<Properties> propertyList = new ArrayList<Properties>();
	public String fname = "Gudrun";
	public String lname = "Gersmann";
	ArrayList<Hashtable<String,RDFNode>> results = new ArrayList<Hashtable<String,RDFNode>>();
	
	/**
	 * <p><em>Title: </em></p>
	 * <p>Description: initiate some properties-Objects for testing</p>
	 *  
	 */
	public void setPropertyList(){
		
		Properties persReqProp = new Properties();
		persReqProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		persReqProp.setProperty("sparqlFile", "gndPersonRequest.txt");
		persReqProp.setProperty("$firstName", "Loki");
		persReqProp.setProperty("$lastName", "Schmidt");
		propertyList.add(persReqProp);

		
		Properties persExtendedReqProp = new Properties();
		persExtendedReqProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		persExtendedReqProp.setProperty("sparqlFile", "gndExtendedPersonRequest.txt");
		persExtendedReqProp.setProperty("$firstName", "Loki");
		persExtendedReqProp.setProperty("$lastName", "Schmidt");
		propertyList.add(persExtendedReqProp);
		
		Properties oaiIssnReqProp = new Properties();
		oaiIssnReqProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		oaiIssnReqProp.setProperty("sparqlFile", "gndExtendedPersonRequest.txt");
		oaiIssnReqProp.setProperty("$issn", "issn: 0031-0182");
		propertyList.add(oaiIssnReqProp);
	}
	
	
	@Test public void request(){	
		
		// create testing properties
		setPropertyList();
		
		// create request threads
		ArrayList<Thread> threadList = new ArrayList<Thread>();
		for(int i=0; i < propertyList.size(); i++){
			Properties reqProp = propertyList.get(i);
			SparqlRunnable spRun = new SparqlRunnable();
			spRun.setProperties(reqProp);
   			Thread sparqlThread = new Thread(spRun);
			sparqlThread.setName("GenericSparqlThread_" + i);
			sparqlThread.start();
			 
		}
		
		
		
		for(int i=0; i < threadList.size(); i++){
			try {
				threadList.get(i).join();
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
		}
		
		System.out.println(results.size());
	    for (int i=0 ; i< results.size() ; i++){
	    	Hashtable<String,RDFNode> soln = results.get(i); 
		    
	    	Enumeration kEnum = soln.keys();
	    	while (kEnum.hasMoreElements()){
	    		System.out.print(soln.get((String)  kEnum.nextElement()) + ": ");
	    		//rKeys.add((String) kEnum.nextElement());
	    	}
	    	
	    	System.out.print("\n");
	    }

/*		System.out.println(results.size());
	    for (int i=0 ; i< results.size() ; i++){
	    	Hashtable<String,RDFNode> soln = results.get(i); 
	    	
	    	// keywords
	    	System.out.print(i +1  + ". " + soln.get("ptitle"));
	    	if(soln.containsKey("page")){
		    	System.out.print(" : Page= " + soln.get("page"));
		    }
	    	else{
	    		System.out.print(" : Nachweis = " + soln.get("publication"));
	    	}
	    	if(soln.containsKey("urn")){
	    		System.out.print(": URN= " + soln.get("urn"));
	    	}
	    	if(soln.containsKey("isbn10")){
	    		System.out.print(": isbn10= " + soln.get("isbn10"));
	    	}
	    	if(soln.containsKey("isbn13")){
	    		System.out.print(": isbn13= " + soln.get("isbn13"));
	    	}
	    	System.out.println();
	    	
	      
	    } */

	}

	public static void main(String[] args) {
		// TODO Auto-generated method stub

		TestGenericSparqlRequest test = new TestGenericSparqlRequest(); 
		test.request();
	}

	public class SparqlRunnable implements Runnable {

		private Properties reqProp = null;
		
		@Override
		public void run() {
			//Test OA Explorer
			GenericSPARQLRequest gsReq = new GenericSPARQLRequest(reqProp);
			ArrayList<Hashtable<String,RDFNode>> oaResults = gsReq.performRequest();
			results.addAll(oaResults);
			
			
		}
		
		public void setProperties(Properties ReqProp){
			reqProp = ReqProp;
		}
		
	}

}
