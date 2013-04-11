/**
 * 
 */
package de.qterra.gnd.sparql.requests;

import com.hp.hpl.jena.rdf.model.RDFNode;
import com.ibm.icu.text.Normalizer;

import de.qterra.gnd.sparql.SparqlQuery;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;
import java.util.Properties;

import org.apache.log4j.Logger;


/**
 * @author aquast
 *
 */
public class GenericSPARQLRequest {

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(PersonRequest.class);

	private Properties requestProp = null;
	private String queryString = null;
	
	
	public GenericSPARQLRequest(Properties RequestProp){
		requestProp = RequestProp;
	}
	/**
	 * Method calls the SPARQL-request
	 * @param discipline
	 * @return
	 */
	public ArrayList<Hashtable<String,RDFNode>> performRequest(){

		// set remote Sparql-Enpoint
		SparqlQuery query = new SparqlQuery(requestProp.getProperty("requestUrl"));

		// load query template from appropriate file
		try{
			InputStream is = getClass().getResourceAsStream("/requestTemplates/" + requestProp.getProperty("sparqlFile"));
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
		
		Enumeration propEnum = requestProp.keys();
		while (propEnum.hasMoreElements()){
			String key = (String) propEnum.nextElement();
			if(key.startsWith("$")){
				queryString = queryString.replace(key, Normalizer.normalize(requestProp.getProperty(key), Normalizer.NFC));
			}
		}
		log.debug(queryString);

		query.setQueryString(queryString);
		ArrayList<Hashtable<String,RDFNode>> results = query.querySparql();
		
		return results;
	}


}
