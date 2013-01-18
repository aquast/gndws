/**
 * 
 */
package de.qterra.gnd.test;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;

import com.hp.hpl.jena.query.QuerySolution;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.rdf.model.RDFNode;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.IssnRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;

/**
 * @author aquast
 *
 */
public class TestSparqlQuery {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		
		SparqlQuery query = null;
		String queryString = null;
		
		//queryString = "select ?uri ?name where \n" +
		//" {?uri <http://RDVocab.info/ElementsGr2/dateOfBirth> \"1735\" .\n" +
		//" ?uri <http://d-nb.info/gnd/preferredNameForThePerson> ?name}";
		
		
		
		/*
		queryString = "select ?uri ?name where \n" +
		" {?uri <http://RDVocab.info/ElementsGr2/dateOfBirth> \"1802\" .\n" +
		" ?uri <http://d-nb.info/gnd/preferredNameForThePerson> ?name}";
		//" ?ano ?n \"Quast\"." +
		//" ?ano ?x ?uri}";
		*/
		
			
		// EXAMPLE Query the OAI Explorer for ISSN

		query =new SparqlQuery("http://oai.rkbexplorer.com/sparql/");
		queryString = "PREFIX id:      <http://oai.rkbexplorer.com/id/>\n" +
				"PREFIX rdf:     <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\n" +
				"PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#>\n" +
				"PREFIX owl:     <http://www.w3.org/2002/07/owl#>\n" +
				"PREFIX foaf:    <http://xmlns.com/foaf/0.1/>\n" +
				"PREFIX dc:      <http://purl.org/dc/elements/1.1/>\n" +
				"PREFIX dcterms: <http://purl.org/dc/terms/>\n" +
				"SELECT DISTINCT ?title ?creator ?uri WHERE { " +
				"?uri dc:identifier \"ISSN: 0031-0182\" .\n" +
				"?uri dc:title ?title .\n" +
				"?uri dcterms:creator ?cid .\n" +
				"?cid foaf:name ?creator \n"+
				"} LIMIT 1000\n";

		// EXAMPLE Query the OAI Explorer for Author
		queryString = "PREFIX id:      <http://oai.rkbexplorer.com/id/>\n" +
				"PREFIX rdf:     <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\n" +
				"PREFIX rdfs:    <http://www.w3.org/2000/01/rdf-schema#>\n" +
				"PREFIX owl:     <http://www.w3.org/2002/07/owl#>\n" +
				"PREFIX foaf:    <http://xmlns.com/foaf/0.1/>\n" +
				"PREFIX dc:      <http://purl.org/dc/elements/1.1/>\n" +
				"PREFIX dcterms: <http://purl.org/dc/terms/>\n" +
				"SELECT DISTINCT ?title ?uri ?ident WHERE { " +
				"?uri dc:identifier ?ident .\n" +
				"?uri dc:title ?title .\n" +
				"?uri dcterms:creator ?cid .\n" +
				"?cid foaf:name \"Michael Scott\" \n"+
				"} LIMIT 1000\n";
		
		/*
		String queryString = "select ?uri where \n" +
		"{" +
		//" ?anoS <http://d-nb.info/gnd/surname> \"Spindler\" ." +
		//" ?anoF <http://d-nb.info/gnd/foreName> \"Gerald\" ." +
		//" ?uri ?x ?anoS ." +
		//" ?uri ?y ?anoF ." +
		" ?uri <http://d-nb.info/gnd/preferredNameForThePerson> \"Dyk, Johann\"}";
		*/
		
		// Test lobid 
		query = new SparqlQuery("http://www.lobid.org/sparql/");
		queryString =
		"PREFIX rdf:      <http://www.w3.org/1999/02/22-rdf-syntax-ns#>\n" +
		"PREFIX rdfs:     <http://www.w3.org/2000/01/rdf-schema#>\n" +
		"PREFIX foaf:     <http://xmlns.com/foaf/0.1/>\n" +
		"PREFIX xsd:      <http://www.w3.org/2001/XMLSchema#>\n" +
		"PREFIX vcard:    <http://www.w3.org/2006/vcard/ns#>\n" +
		"PREFIX dc:       <http://purl.org/dc/terms/>\n" +
		"PREFIX skos:     <http://www.w3.org/2004/02/skos/core#>\n" +
		"PREFIX frbr:     <http://purl.org/vocab/frbr/core#>\n" +
		"PREFIX bibo:     <http://purl.org/ontology/bibo/>\n" +
		"PREFIX geo:      <http://www.w3.org/2003/01/geo/wgs84_pos#>\n" +
		"PREFIX gr:       <http://purl.org/goodrelations/v1#>\n" +
		"SELECT * WHERE { " +
		"?s ?p ?o " +
		"} LIMIT 10\n";

		// Test b3kat 
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
		"?uri dc:description \"Gudrun Gersmann\" .\n" +
		"?uri dc:title ?title .\n" +
		//"?cid foaf:name ?creator \n"+
		"} LIMIT 1000\n";

		
		
		//Set queryString to SparqlQuery and perform request
		query.setQueryString(queryString);
		ArrayList<Hashtable<String,RDFNode>> results = query.querySparql();

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

	}

}
