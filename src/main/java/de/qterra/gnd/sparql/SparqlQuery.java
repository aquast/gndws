/**
 * 
 */
package de.qterra.gnd.sparql;

import java.util.ArrayList;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;

import org.apache.log4j.Logger;

import com.hp.hpl.jena.db.DBConnection;
import com.hp.hpl.jena.query.Query;
import com.hp.hpl.jena.query.QueryExecution;
import com.hp.hpl.jena.query.QueryExecutionFactory;
import com.hp.hpl.jena.query.QueryFactory;
import com.hp.hpl.jena.query.QuerySolution;
import com.hp.hpl.jena.query.ResultSet;
import com.hp.hpl.jena.rdf.model.Model;
import com.hp.hpl.jena.rdf.model.ModelFactory;
import com.hp.hpl.jena.rdf.model.ModelMaker;
import com.hp.hpl.jena.rdf.model.RDFNode;
import com.hp.hpl.jena.sparql.engine.http.Service;

import de.qterra.gnd.sparql.requests.PersonRequest;
import de.qterra.gnd.util.Connector;

/**
 * Class SparqlQuery
 * 
 * <p><em>Title: </em></p>
 * <p>Description: </p>
 * 
 * @author aquast, email
 * creation date: 20.12.2010
 *
 */
public class SparqlQuery {

	// Initiate Logger for Class
	private static Logger log = Logger.getLogger(SparqlQuery.class);

	private static Model gnd = ModelFactory.createDefaultModel();
	private ModelMaker conModel;
	private String queryString;
	private String service = null;
	
	
	
	/**
	 * Constructor SparqlQuery.java
	 * @param Service
	 * 
	 *<p><em>Title: </em> Class serves requests to a SPARQL-Endpoint</p>
	 * <p>Description: Constructor takes the SPARQL-Endpoint as String and submits the request
	 * defined in a Class defined in request-Package to this SPARQL-Endpoint</p>
	 *  
	 */
	public SparqlQuery(String Service){
		service = Service;
	}

	/**
	 * <p><em>Title: Query SparQL Interface</em></p>
	 * <p>Description: Method queries a given Sparql-Interface defined by 
	 * Connector properties. queryString has to be set for the request.
	 * Returns an Array of RDFNodes</p>
	 * 
	 * @return 
	 */
	public ArrayList<Hashtable<String,RDFNode>> querySparql(){

		ArrayList<Hashtable<String,RDFNode>> resultArray = new ArrayList<Hashtable<String,RDFNode>>();
		ResultSet results = null;
		
		Query query = QueryFactory.create(queryString);
		QueryExecution qexec = QueryExecutionFactory.sparqlService(service, query);
		//QueryExecution qexec = QueryExecutionFactory.create(query, gnd);
		try {
		    results = qexec.execSelect();
		    List<String> varName = results.getResultVars();
		    //log.info("VarnameSize: " + varName.size());
		    for ( ; results.hasNext() ; )
		    {
		      
		      QuerySolution soln = results.nextSolution() ;
    		  Hashtable<String,RDFNode> rCols = new Hashtable<String,RDFNode>();
		      //ArrayList<RDFNode> solnArray = new ArrayList<RDFNode>();
		      ArrayList<Hashtable<String,RDFNode>> solnArray = new ArrayList<Hashtable<String,RDFNode>>();
    		  
    		  boolean noAnon = true;
    		  Iterator<String> vName = soln.varNames();
    		  while(vName.hasNext()){
 		    	  // reduce the result size: 
		    	  // test if any of the columns have an anonUri, 
		    	  // if so, not do not add the Solution 
		    	  String vStr = vName.next(); 
	    		  rCols.put(vStr, soln.get(vStr));
		    	  //solnArray.add(rCols);
    			  if (soln.get(vStr).isAnon()){		    		  
    				  noAnon = false;
    			 }
		      }
	    	  
    		  if (noAnon){
	    		  resultArray.add(rCols);
	    		  //log.info(resultArray.size());
	    	  }
		    }
		    log.info("Ergebnisliste: " + resultArray.size());
		  }
		catch(Exception e){
			log.error(e);			
		}
		finally { qexec.close() ; }
		return resultArray;
	}

	/**
	 * <p><em>Title: Setter queryString</em></p>
	 * <p>Description:  Setter for queryString</p>
	 * 
	 * @param queryString 
	 */
	public void setQueryString(String queryString) {
		this.queryString = queryString;
		log.info(queryString);
	}

}
