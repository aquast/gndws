/**
 * 
 */
package de.qterra.gnd.test;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Properties;

import org.apache.log4j.Logger;
import org.junit.Test;

import com.hp.hpl.jena.query.QuerySolution;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.sparql.requests.B3KatContentByPersonRequest;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.GenericSPARQLRequest;
import de.qterra.gnd.sparql.requests.IssnRequest;
import de.qterra.gnd.sparql.requests.OAContentByPersonRequest;
import de.qterra.gnd.sparql.requests.OpenLibContentByPersonRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;
import de.qterra.gnd.sparql.util.UnifyResults;
import de.qterra.gnd.webservice.ResourceResultType;

/**
 * @author aquast
 *
 */
public class TestUnifyResults {


	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(TestUnifyResults.class);

 	/**
 	 * <p><em>Title: </em></p>
 	 * <p>Description: Method verifies that Class UnifyResults is working correctly</p>
 	 *  
 	 */
 	//@Test 
 	public void testRequests() {

		ArrayList<Hashtable<String, RDFNode>> results = new ArrayList<Hashtable<String, RDFNode>>();
		
		
		String isbn = null;
		int isbnType = 0;
		String idType = "isbn";
		String id = "978-3-86509-791-0";
				
		if(idType.equals("isbn")){
			isbn = id.replace("-", "");
			isbnType = isbn.length();
			log.info(isbnType);
		}
		
		ArrayList<Properties> propertyList = new ArrayList<Properties>();

		Properties gndResByIsbnProp = new Properties();
		gndResByIsbnProp.setProperty("requestUrl", "http://lobid.org/sparql/");
		gndResByIsbnProp.setProperty("sparqlFile", "gndResourcesByIsbn" + isbnType + "Request.txt");
		gndResByIsbnProp.setProperty("$isbn", isbn);
		propertyList.add(gndResByIsbnProp);
		
		GenericSPARQLRequest gRequest = new GenericSPARQLRequest(gndResByIsbnProp);
		results = gRequest.performRequest();
		
		ArrayList<ResourceResultType> resultArray = new ArrayList<ResourceResultType>();


	    
	    ArrayList<String> comp = new ArrayList<String>();
	    //comp.add("title");
	    comp.add("uri");
	    //comp.add("uri");
	    UnifyResults uni = new UnifyResults();
	    
	    uni.setComparator(comp);
	    uni.setResults(results);
	    //ArrayList<Hashtable<String,ArrayList<String>>> unifiedResults = uni.unify();
	    ArrayList<Hashtable<String, Hashtable<String, ArrayList<String>>>> unifiedResults = uni.unify("uri");
	    

	    System.out.println("\n\nZusammengeführte Ergebnisse: " + unifiedResults.size());

	    
	    for (int i=0 ; i< unifiedResults.size() ; i++){
	    	Hashtable<String, Hashtable<String,ArrayList<String>>> uResult = unifiedResults.get(i); 
		    
	    	Enumeration<String> kEnum = uResult.keys();
    		int k = 1;
	    	while (kEnum.hasMoreElements()){
	    		String key = kEnum.nextElement();
	    		System.out.println(k++ + " " + key + " :");
	    		for(int j = 0; j < uResult.get(key).size(); j++){
	    			System.out.print("....................." + uResult.get(key).get(j) + " --- ");
	    			System.out.println("");
	    		}
	    	}
	    	
	    	System.out.print("\n");
	    }
	}

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		TestUnifyResults tReq = new TestUnifyResults();
		tReq.testRequests();

	}

}
