/**
 * 
 */
package de.qterra.gnd.sparql.requests;

import com.hp.hpl.jena.query.QuerySolution;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.rdf.model.RDFNode;
import com.ibm.icu.text.Normalizer;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.util.Connector;

import java.util.ArrayList;
import java.util.Hashtable;

//import org.openjena.atlas.logging.Log;
import org.apache.log4j.Logger;


/**
 * @author aquast
 *
 */
public class OpenLibContentByPersonRequest {

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(OpenLibContentByPersonRequest.class);

	/**
	 * Method generates a Query to the oai triple store of ... for finding 
	 * OA Ressources within all availabe repositories by the persons name. 
	 * It then performs the request as a Wrapper for the Requests
	 * Request Performing is included here because two requests are necessary
	 * sometimes. This fact should be transparent to the Web Service  
	 * @param firstName
	 * @param secondName
	 * @return
	 */
	public ArrayList<Hashtable<String,RDFNode>> performRequest(String firstName, String lastName){
		
		SparqlQuery query = new SparqlQuery("http://api.talis.com/stores/openlibrary/services/sparql");

		String decompFirstName = Normalizer.normalize(firstName, Normalizer.NFKD);
		String decompLastName = Normalizer.normalize(lastName, Normalizer.NFKD);
		
		log.info(decompLastName);
		log.info(decompFirstName);

        String queryStringSimple = 
        	"PREFIX id:      <http://oai.rkbexplorer.com/id/>" +
        	"PREFIX rdf:     <http://www.w3.org/1999/02/22-rdf-syntax-ns#>" +
        	"PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#>" +
        	"PREFIX owl:     <http://www.w3.org/2002/07/owl#>" +
        	"PREFIX foaf:    <http://xmlns.com/foaf/0.1/>" + 
        	"PREFIX dc:      <http://purl.org/dc/elements/1.1/>" +
        	"PREFIX dcterms: <http://purl.org/dc/terms/>" +
    		"SELECT DISTINCT * WHERE { ?creator foaf:name '" + 
    		decompFirstName + " " + decompLastName + "' ." +
    		"?creator foaf:made ?publication ." +
    		"?publication dcterms:title ?title ." +
    		"?publication dcterms:identifier ?identifier \n" +
    		//"?publication dcterms:hasVersion ?work ." +
    		//"?publication dcterms:identifier ?id ." +
    		//"OPTIONAL {?work foaf:page ?page} ." +
    		//"OPTIONAL {?work <http://purl.org/ontology/bibo/isbn10> ?isbn10} . " +
    		//"OPTIONAL {?work <http://purl.org/ontology/bibo/isbn13> ?isbn13} . " +
    		"} LIMIT 1000";        
        
		
		query.setQueryString(queryStringSimple);
		ArrayList<Hashtable<String,RDFNode>> results = query.querySparql();

		return results;
	}
}
