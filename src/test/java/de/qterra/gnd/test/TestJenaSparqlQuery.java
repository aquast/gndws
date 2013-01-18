/**
 * 
 */
package de.qterra.gnd.test;

import java.util.ArrayList;
import java.util.Hashtable;

import com.hp.hpl.jena.query.QuerySolution;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.sparql.JenaSparqlQuery;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;

/**
 * @author aquast
 *
 */
public class TestJenaSparqlQuery {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		JenaSparqlQuery query = new JenaSparqlQuery();

		
		//String queryString = "select ?uri ?name where \n" +
		//" {?uri <http://RDVocab.info/ElementsGr2/dateOfBirth> \"1735\" .\n" +
		//" ?uri <http://d-nb.info/gnd/preferredNameForThePerson> ?name}";
		
		
		/*
		String queryString = "select ?uri ?name where \n" +
		" {?uri <http://RDVocab.info/ElementsGr2/dateOfBirth> \"1802\" .\n" +
		" ?uri <http://d-nb.info/gnd/preferredNameForThePerson> ?name}";
		//" ?ano ?n \"Quast\"." +
		//" ?ano ?x ?uri}";
		*/
		
		
		String queryString = "select distinct ?uri ?name where \n" +
		"{" +
		" ?anoS <http://d-nb.info/gnd/surname> \"Schmidt\" ." +
		" ?anoF <http://d-nb.info/gnd/foreName> \"Helmut\" ." +
		" ?uri ?x ?anoS ." +
		" ?uri ?x ?anoF ." +
		" ?uri <http://d-nb.info/gnd/preferredNameForThePerson> ?name}";
		

		/*
		String queryString = "select ?uri where \n" +
		"{" +
		//" ?anoS <http://d-nb.info/gnd/surname> \"Spindler\" ." +
		//" ?anoF <http://d-nb.info/gnd/foreName> \"Gerald\" ." +
		//" ?uri ?x ?anoS ." +
		//" ?uri ?y ?anoF ." +
		" ?uri <http://d-nb.info/gnd/preferredNameForThePerson> \"Dyk, Johann\"}";
		*/

		//Test querySparql Class
		//query.setQueryString(queryString);
		//ArrayList<ArrayList<RDFNode>> results = query.querySparql();
		
		//Test PersonRequest
		//PersonRequest perReq = new PersonRequest();
		//ArrayList<ArrayList<RDFNode>> results = perReq.performRequest("Gerald", "Hoffmann");
		
		//Test ClassificationRequest
		ClassificationRequest classReq = new ClassificationRequest();
		ArrayList<Hashtable<String,RDFNode>> results = classReq.performRequest("Isotop");
		
		//Test querySparql Class
		//query.setQueryString(queryString);
		//ArrayList<ArrayList<RDFNode>> results = query.querySparql();
		
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
