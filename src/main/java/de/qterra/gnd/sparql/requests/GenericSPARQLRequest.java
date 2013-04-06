/**
 * 
 */
package de.qterra.gnd.sparql.requests;

import com.hp.hpl.jena.rdf.model.RDFNode;
import com.ibm.icu.text.Normalizer;

import de.qterra.gnd.sparql.SparqlQuery;

import java.util.ArrayList;
import java.util.Hashtable;


/**
 * @author aquast
 *
 */
public class GenericSPARQLRequest {

	private String requestUrl = null;
	private ArrayList<String> queryArguments = null;
	private String queryPrefix = null;
	
	
	/**
	 * Method calls the SPARQL-request
	 * @param discipline
	 * @return
	 */
	public ArrayList<Hashtable<String,RDFNode>> performRequest(String discipline){

		SparqlQuery query = new SparqlQuery(requestUrl);

		String decompDiscipline = Normalizer.normalize(discipline, Normalizer.NFKD);
		
		System.out.println(decompDiscipline);
		
		

		String queryStringSimple = "select ?uri ?pred ?obj where \n" +
		"{" +
		" ?uri ?pred \"" + decompDiscipline  + "\"@de ." +
		" ?uri <http://www.w3.org/2004/02/skos/core#prefLabel> ?obj ." +
		"}";
		

		query.setQueryString(queryStringSimple);
		ArrayList<Hashtable<String,RDFNode>> results = query.querySparql();
		
		if (results.size()<1){
			//query.setQueryString(queryStringExtended);
			//results = query.querySparql();
		}

		return results;
	}

	public void parseArguments(){
		
	}


}
