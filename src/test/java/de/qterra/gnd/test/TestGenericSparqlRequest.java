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

import org.apache.log4j.Logger;
import org.junit.Test;
import org.openjena.atlas.logging.Log;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.GenericSPARQLRequest;
import de.qterra.gnd.sparql.requests.IssnRequest;
import de.qterra.gnd.sparql.requests.OAContentByPersonRequest;
import de.qterra.gnd.sparql.requests.OpenLibContentByPersonRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;

/**
 * @author aquast
 *
 */
public class TestGenericSparqlRequest{

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(TestGenericSparqlRequest.class);

	private ArrayList<Properties> propertyList = new ArrayList<Properties>();
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
		//propertyList.add(persReqProp);

		Properties persExtendedReqProp = new Properties();
		persExtendedReqProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		persExtendedReqProp.setProperty("sparqlFile", "gndExtendedPersonRequest.txt");
		persExtendedReqProp.setProperty("$firstName", "Loki");
		persExtendedReqProp.setProperty("$lastName", "Schmidt");
		//propertyList.add(persExtendedReqProp);
		
		Properties oaiIssnReqProp = new Properties();
		oaiIssnReqProp.setProperty("requestUrl", "http://oai.rkbexplorer.com/sparql/");
		oaiIssnReqProp.setProperty("sparqlFile", "oaiExplorerIssnRequest.txt");
		oaiIssnReqProp.setProperty("$issn", "ISSN: 0031-0182");
		//propertyList.add(oaiIssnReqProp);

		
		Properties b3katIssnReqProp = new Properties();
		b3katIssnReqProp.setProperty("requestUrl", "http://lod.b3kat.de/sparql");
		b3katIssnReqProp.setProperty("sparqlFile", "b3katIssnRequest.txt");
		b3katIssnReqProp.setProperty("$issn", "ISSN: 0031-0182");
		//propertyList.add(b3katIssnReqProp);

		Properties b3katPersResReqProp = new Properties();
		b3katPersResReqProp.setProperty("requestUrl", "http://lod.b3kat.de/sparql");
		b3katPersResReqProp.setProperty("sparqlFile", "b3katPersonResourcesRequest.txt");
		b3katPersResReqProp.setProperty("$pnd", "<http://d-nb.info/gnd/112537316>");
		propertyList.add(b3katPersResReqProp);

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
		
		System.out.println("Trefferanzahl: " + results.size());
	    for (int i=0 ; i< results.size() ; i++){
	    	Hashtable<String,RDFNode> soln = results.get(i); 
		    
	    	Enumeration kEnum = soln.keys();
	    	while (kEnum.hasMoreElements()){
	    		String key = (String) kEnum.nextElement();
	    		System.out.print(key + " = " + soln.get(key) + "; ");
	    		//rKeys.add((String) kEnum.nextElement());
	    	}
	    	
	    	System.out.print("\n");
	    }


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
			GenericSPARQLRequest gsReq = new GenericSPARQLRequest(reqProp);
			ArrayList<Hashtable<String,RDFNode>> sparqlResults = gsReq.performRequest();
			results.addAll(sparqlResults);
			
			
		}
		
		public void setProperties(Properties ReqProp){
			reqProp = ReqProp;
		}
		
	}

}
