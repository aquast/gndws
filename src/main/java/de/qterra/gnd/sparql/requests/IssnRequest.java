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
public class IssnRequest {

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(IssnRequest.class);

	/**
	 * Method generates a Query for Personal information. 
	 * It then performs the request as a Wrapper for Person Requests
	 * Request Performing is included here because two requests are necessary
	 * sometimes. This fact should be opaque to the Web Service  
	 * @param firstName
	 * @param secondName
	 * @return
	 */
	public ArrayList<Hashtable<String,RDFNode>> performRequest(String issn){
		
		ArrayList<Hashtable<String,RDFNode>> results = new ArrayList<Hashtable<String,RDFNode>>(); 
		String queryString = null;
		
		SparqlQuery query = new SparqlQuery("http://oai.rkbexplorer.com/sparql/");

		
		ArrayList<String> issnArray = new ArrayList<String>();
		issnArray.add(issn);
		issnArray.add("ISSN: " + issn);
		issnArray.add("issn: " + issn);
		issnArray.add(issn.replace("-", ""));
		
		
		for(int i = 0; i < issnArray.size(); i++){
			// Query the OAI Explorer for ISSN
			queryString = "PREFIX id:      <http://oai.rkbexplorer.com/id/>\n" +
					"PREFIX rdf:     <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\n" +
					"PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#>\n" +
					"PREFIX owl:     <http://www.w3.org/2002/07/owl#>\n" +
					"PREFIX foaf:    <http://xmlns.com/foaf/0.1/>\n" +
					"PREFIX dc:      <http://purl.org/dc/elements/1.1/>\n" +
					"PREFIX dcterms: <http://purl.org/dc/terms/>\n" +
					"SELECT DISTINCT * WHERE { " +
					"?uri dc:identifier \"" + issnArray.get(i) + "\" .\n" +
					"?uri dc:identifier ?identifier .\n" +
					"?uri dc:title ?title \n" +
					//"?uri dcterms:creator ?cid .\n" +
					//"?cid foaf:name ?creator \n"+
					"} LIMIT 1000\n";			

			query.setQueryString(queryString);
			ArrayList<Hashtable<String,RDFNode>> resultPart = null;
			resultPart = query.querySparql();
			results.addAll(resultPart);
		}

		for(int i = 0; i < issnArray.size(); i++){

		query = new SparqlQuery("http://lod.b3kat.de/sparql");
		queryString =
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
			"?uri bibo:issn \"" + issnArray.get(i) + "\" .\n" +
			"?uri dc:identifier ?identifier .\n" +
			"?uri dc:title ?title \n"+
			"} LIMIT 1000\n";

			query.setQueryString(queryString);
			ArrayList<Hashtable<String,RDFNode>> resultPart = null;
			resultPart = query.querySparql();
			results.addAll(resultPart);
		
		}
		return results;
	}
}
