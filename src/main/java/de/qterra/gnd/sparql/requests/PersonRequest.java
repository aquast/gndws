/**
 * 
 */
package de.qterra.gnd.sparql.requests;

import com.hp.hpl.jena.rdf.model.RDFNode;
import com.ibm.icu.text.Normalizer;

import de.qterra.gnd.sparql.SparqlQuery;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.util.ArrayList;
import java.util.Hashtable;

//import org.openjena.atlas.logging.Log;
import org.apache.log4j.Logger;


/**
 * @author aquast
 *
 */
public class PersonRequest {

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(PersonRequest.class);

	/**
	 * Method generates a Query for Personal information. 
	 * It then performs the request as a Wrapper for Person Requests
	 * Request Performing is included here because two requests are necessary
	 * sometimes. This fact should be opaque to the Web Service  
	 * @param firstName
	 * @param secondName
	 * @return
	 */
	public ArrayList<Hashtable<String,RDFNode>> performRequest(String firstName, String lastName){
		
		
		SparqlQuery query = new SparqlQuery("http://lobid.org/sparql/");

		String decompFirstName = Normalizer.normalize(firstName, Normalizer.NFKD);
		String decompLastName = Normalizer.normalize(lastName, Normalizer.NFKD);
		
		log.info(decompLastName);
		log.info(decompFirstName);
		
		String queryStringExtended = "select distinct ?uri ?name ?birth ?link ?acad where \n" +
		"{" +
        " ?uri <http://d-nb.info/standards/elementset/gnd#variantNameForThePerson> \"" + decompLastName + ", " + decompFirstName + "\" ." +
		//" ?anoS <http://d-nb.info/standards/elementset/gnd#surname> \"" +  decompLastName + "\" ." +
		//" ?anoF <http://d-nb.info/standards/elementset/gnd#forename> \"" + decompFirstName + "\" ." +
		//" ?uri ?x ?anoS ." +
		//" ?uri ?x ?anoF ." +
		" ?uri <http://d-nb.info/standards/elementset/gnd#preferredNameForThePerson> ?name .\n" +
        " OPTIONAL {?uri <http://d-nb.info/standards/elementset/gnd#dateOfBirth> ?birth} .\n" +
        " OPTIONAL {?uri <http://d-nb.info/standards/elementset/gnd#biographicalOrHistoricalInformation> ?biogr} .\n" +
        //" OPTIONAL {?uri <http://RDVocab.info/ElementsGr2/professionOrOccupation> ?profess} .\n" +
        //" OPTIONAL {?profess  <http://www.w3.org/2004/02/skos/core#prefLabel> ?occu} .\n" +
        " OPTIONAL {?uri <http://d-nb.info/standards/elementset/gnd#academicTitleOfThePerson> ?acad} .\n" +
		" OPTIONAL {?uri <http://xmlns.com/foaf/0.1/page> ?link} " +
		"}";
		

        /*String queryStringSimple = "select distinct ?uri ?name ?name1 ?birth ?link ?biogr ?acad where \n" +
        "{" +
        " ?uri <http://d-nb.info/standards/elementset/gnd#preferredNameForThePerson> \"" + decompLastName + ", " + decompFirstName + "\" . \n" +
        " ?uri <http://d-nb.info/standards/elementset/gnd#preferredNameForThePerson> ?name . \n" +
        " OPTIONAL {?uri <http://d-nb.info/standards/elementset/gnd#dateOfBirth> ?birth} .\n" +
        " OPTIONAL {?uri <http://d-nb.info/standards/elementset/gnd#biographicalOrHistoricalInformation> ?biogr} .\n" +
        //" OPTIONAL {?uri <http://RDVocab.info/ElementsGr2/professionOrOccupation> ?profess} .\n" +
        //" OPTIONAL {?profess  <http://www.w3.org/2004/02/skos/core#prefLabel> ?occu} .\n" +
        " OPTIONAL {?uri <http://d-nb.info/standards/elementset/gnd#academicTitleOfThePerson> ?acad} .\n " +
        " OPTIONAL {?uri <http://xmlns.com/foaf/0.1/page> ?link} " +
        "}"; */
		
		String queryStringSimple = null;
		
		try{
			File persRequest = new File("META-INF/gndPersonRequest.txt");
			FileInputStream fis = new FileInputStream(persRequest);
			BufferedInputStream bis = new BufferedInputStream(fis);
			
			ByteArrayOutputStream bas = new ByteArrayOutputStream();
			int i = 0;
			while ((i = bis.read()) !=-1){
				bas.write(i);
				queryStringSimple = bas.toString("UTF-8");
			}
			
		}catch(Exception e){
				log.error(e);
		}
		
		
		queryStringSimple = queryStringSimple.replace("$lastName", decompLastName);
		queryStringSimple = queryStringSimple.replace("$firstName", decompFirstName);
			
		query.setQueryString(queryStringSimple);
		ArrayList<Hashtable<String,RDFNode>> results = query.querySparql();
		
		if (results.size()<=1){
			query.setQueryString(queryStringExtended);
			results.addAll(query.querySparql());
		}

		return results;
	}
}
