/**
 * 
 */
package de.qterra.gnd.sparql.requests;

import com.hp.hpl.jena.query.QuerySolution;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.rdf.model.RDFNode;
import com.ibm.icu.text.Normalizer;

import de.qterra.gnd.sparql.JenaSparqlQuery;

import java.util.ArrayList;
import java.util.Hashtable;

import org.openjena.atlas.logging.Log;

/**
 * @author aquast
 *
 */
public class ClassificationRequest {

	/**
	 * Method generates a Query for Discipline information. 
	 * It then performs the request as a Wrapper for Discipline Requests
	 * Request Performing is included here because two requests are necessary
	 * sometimes. This fact should be opaque to the Web Service  
	 * @param discipline
	 * @return
	 */
	public ArrayList performRequest(String discipline){

		JenaSparqlQuery query = new JenaSparqlQuery();

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
}
