/**
 * 
 */
package de.qterra.gnd.test;

import java.util.ArrayList;
import java.util.Hashtable;

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
public class TestServiceSparqlQuery {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		SparqlQuery query = new SparqlQuery("http://oai.rkbexplorer.com/sparql/");

		
		
		String queryString = 
	       	"PREFIX id:      <http://oai.rkbexplorer.com/id/> \n" +
	       	"PREFIX rdf:     <http://www.w3.org/1999/02/22-rdf-syntax-ns#> \n" +
	       	"PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#> \n" +
	       	"PREFIX owl:     <http://www.w3.org/2002/07/owl#> \n" +
	       	"PREFIX foaf:    <http://xmlns.com/foaf/0.1/> \n" + 
	       	"PREFIX dc:      <http://purl.org/dc/elements/1.1/> \n" +
	       	"PREFIX dcterms: <http://purl.org/dc/terms/> \n" +
	       	"select ?s ?p where \n" +
	        "{" +
	        "?s dc:creator ?p . \n" +
	        "} \n" +
	        "LIMIT 10 \n";
	        
		

		//Test ServiceQuerySparql Class
		query.setQueryString(queryString);
		ArrayList<Hashtable<String, RDFNode>> results = query.querySparql();
		
		System.out.println(results.size());
	    for (int i=0 ; i< results.size() ; i++){
	    	Hashtable<String,RDFNode> soln = results.get(i); 
	    	
	    	System.out.print(soln.get("uri") + ": ");
	    	System.out.print(soln.get("label"));
	    	if(soln.containsKey("nLabel")){
		    	System.out.println(": engerer Begriff = " + soln.get("nLabel"));
		    }
	    	else{
	    		System.out.println("");
	    	}
	    	//ArrayList soln =  results.get(i);
	    	/*for(int j=0; j<soln.size(); j++){
	    		System.out.print(soln.get(j) + " ; ");
	    	}*/
	    	//System.out.println(" ");
	      
	    }


	}

}
