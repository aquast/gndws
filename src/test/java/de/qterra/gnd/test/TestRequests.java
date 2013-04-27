/**
 * 
 */
package de.qterra.gnd.test;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;

import org.junit.Test;

import com.hp.hpl.jena.query.QuerySolution;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.sparql.requests.B3KatContentByPersonRequest;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.IssnRequest;
import de.qterra.gnd.sparql.requests.OAContentByPersonRequest;
import de.qterra.gnd.sparql.requests.OpenLibContentByPersonRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;
import de.qterra.gnd.sparql.util.UnifyResults;

/**
 * @author aquast
 *
 */
public class TestRequests {

	
 	/**
 	 * <p><em>Title: </em></p>
 	 * <p>Description: Method verifies that Class UnifyResults is working correctly</p>
 	 *  
 	 */
 	//@Test 
 	public void testRequests() {

		ArrayList<Hashtable<String, RDFNode>> results = new ArrayList<Hashtable<String, RDFNode>>();
		
		String queryString = null;
		
		
		//Test PersonRequest
		PersonRequest perReq = new PersonRequest();
		results = perReq.performRequest("Gerhard", "Hoffmann");


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
	    
	    ArrayList<String> comp = new ArrayList<String>();
	    //comp.add("title");
	    comp.add("name");
	    //comp.add("uri");
	    UnifyResults uni = new UnifyResults();
	    
	    uni.setComparator(comp);
	    uni.setResults(results);
	    ArrayList<Hashtable<String,ArrayList<String>>> unifiedResults = uni.unify();

	    System.out.println("\n\nZusammengeführte Ergebnisse: " + unifiedResults.size());

	    for (int i=0 ; i< unifiedResults.size() ; i++){
	    	Hashtable<String,ArrayList<String>> uResult = unifiedResults.get(i); 
		    
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
		TestRequests tReq = new TestRequests();
		tReq.testRequests();

	}

}
