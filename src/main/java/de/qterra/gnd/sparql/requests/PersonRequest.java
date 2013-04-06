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
import java.io.InputStream;
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
		
		
		String queryString = null;
		
		try{
			// read in appropriate request String from text file
			InputStream is = getClass().getResourceAsStream("/requestTemplates/gndPersonRequest.txt");
			BufferedInputStream bis = new BufferedInputStream(is);
			
			ByteArrayOutputStream bas = new ByteArrayOutputStream();
			int i = 0;
			while ((i = bis.read()) !=-1){
				bas.write(i);
				queryString = bas.toString("UTF-8"); 
			} 
			
		}catch(Exception e){
				log.error(e);
		}
		
		
		queryString = queryString.replace("$lastName", decompLastName);
		queryString = queryString.replace("$firstName", decompFirstName);
			
		query.setQueryString(queryString);
		ArrayList<Hashtable<String,RDFNode>> results = query.querySparql();
		
		if (results.size()<=10){
			try{
				// read in appropriate request String from text file
				InputStream is = getClass().getResourceAsStream("/requestTemplates/gndPersonRequestExtended.txt");
				BufferedInputStream bis = new BufferedInputStream(is);
				
				ByteArrayOutputStream bas = new ByteArrayOutputStream();
				int i = 0;
				while ((i = bis.read()) !=-1){
					bas.write(i);
					queryString = bas.toString("UTF-8"); 
				} 
				
			}catch(Exception e){
					log.error(e);
			}
			
			
			queryString = queryString.replace("$lastName", decompLastName);
			queryString = queryString.replace("$firstName", decompFirstName);
				
			query.setQueryString(queryString);
			results.addAll(query.querySparql());
		}

		return results;
	}
}
