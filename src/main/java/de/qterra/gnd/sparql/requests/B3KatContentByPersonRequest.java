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
public class B3KatContentByPersonRequest {

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(B3KatContentByPersonRequest.class);

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
		
		SparqlQuery query = new SparqlQuery("http://lod.b3kat.de/sparql");

		String decompFirstName = Normalizer.normalize(firstName, Normalizer.NFKD);
		String decompLastName = Normalizer.normalize(lastName, Normalizer.NFKD);
		
		log.info(decompLastName);
		log.info(decompFirstName);

		query = new SparqlQuery("http://lod.b3kat.de/sparql");
		String queryString =
		"PREFIX rdf:      <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\n" +
		"PREFIX rdfs:     <http://www.w3.org/2000/01/rdf-schema#>\n" +
		"PREFIX owl:	  <http://www.w3.org/2002/07/owl#>\n" +
		"PREFIX dcterms:  <http://purl.org/dc/terms/>\n" +
		"PREFIX foaf:     <http://xmlns.com/foaf/0.1/>\n" +
		"PREFIX dc:       <http://purl.org/dc/elements/1.1/>\n" +
		"PREFIX skos:     <http://www.w3.org/2004/02/skos/core#>\n" +
		"PREFIX frbr:     <http://purl.org/vocab/frbr/core#>\n" +
		"PREFIX bibo:     <http://purl.org/ontology/bibo/>\n" +
		"PREFIX geonames:<http://www.geonames.org/ontology#>\n" +
		"PREFIX dcmitype:<http://purl.org/dc/dcmitype/>\n" +
		"PREFIX marcrel:<http://id.loc.gov/vocabulary/relators/>\n" +
		"PREFIX event:<http://purl.org/NET/c4dm/event.owl#>\n" +
		"SELECT DISTINCT * WHERE { " +
		"?uri dc:description '" +
		decompFirstName + " " + decompLastName + "' ." +
		"?publication dc:title ?title .\n" +
		"?publication dc:creator ?creator .\n" +
		"?publication dc:identifier ?identifier \n" +
		//"?cid foaf:name ?creator \n"+
		"} LIMIT 1000\n";
        
		
		query.setQueryString(queryString);
		ArrayList<Hashtable<String,RDFNode>> results = query.querySparql();

		return results;
	}
}
