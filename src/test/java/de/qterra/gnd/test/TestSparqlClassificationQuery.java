/**
 * 
 */
package de.qterra.gnd.test;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Hashtable;
import java.util.Iterator;

import com.hp.hpl.jena.query.QuerySolution;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;

/**
 * @author aquast
 *
 */
public class TestSparqlClassificationQuery {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub

		
		//Test ClassificationRequest
		ClassificationRequest classReq = new ClassificationRequest();
		ArrayList<Hashtable<String,RDFNode>> results = classReq.performRequest("Isotop");
		
		
		System.out.println(results.size());
	    for (int i=0 ; i< results.size() ; i++){
	    	Hashtable<String,RDFNode> soln = results.get(i); 
	    	
	    	// keywords
	    	System.out.print(soln.get("uri") + ": ");
	    	System.out.print(soln.get("label"));
	    	if(soln.containsKey("nLabel")){
		    	System.out.println(": engerer Begriff = " + soln.get("nLabel"));
		    }
	    	else{
	    		System.out.println("");
	    	}
	    	
	      
	    }


	}

}
