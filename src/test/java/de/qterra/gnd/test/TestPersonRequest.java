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
public class TestPersonRequest {


	/**
	 * <p><em>Title: Method Tests if Class PersonRequest is correctly initiated</em></p>
	 * <p>Description: Method calls the PersonRequest.Class and check for 
	 * functionality of this class</p>
	 *  
	 */
	//@Test 
	public void testPersonRequest() {
		// TODO Auto-generated method stub
		
		ArrayList<Hashtable<String, RDFNode>> results = new ArrayList<Hashtable<String, RDFNode>>();

		
		//Test PersonRequest
		PersonRequest perReq = new PersonRequest();
		//results = perReq.performRequest("Andres", "Quast");
		results = perReq.performRequest("Björn", "Quast");


		System.out.println(results.size());
	    
		// This part prints out all fields found in the Result-Hashtable:
		for (int i=0 ; i< results.size() ; i++){
	    	Hashtable<String,RDFNode> soln = results.get(i); 
		    
	    	Enumeration kEnum = soln.keys();
	    	while (kEnum.hasMoreElements()){
	    		System.out.print(soln.get((String)  kEnum.nextElement()) + ": ");
	    	}
	    	
	    	System.out.print("\n");
	    }
	    
	    	
	}
	
	public static void main(String[] args){
		TestPersonRequest tpTest = new TestPersonRequest();
		tpTest.testPersonRequest();
	}

}
