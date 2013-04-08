/**
 * 
 */
package de.qterra.gnd.test;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Properties;

import com.hp.hpl.jena.rdf.model.RDFNode;

import org.junit.Test;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.sparql.requests.ClassificationRequest;
import de.qterra.gnd.sparql.requests.OAContentByPersonRequest;
import de.qterra.gnd.sparql.requests.OpenLibContentByPersonRequest;
import de.qterra.gnd.sparql.requests.PersonRequest;

/**
 * @author aquast
 *
 */
public class TestSparqlEndpointQuery{

	/**
	 * @param args
	 */
	public String fname = "Gudrun";
	public String lname = "Gersmann";
	ArrayList<Hashtable<String,RDFNode>> results = new ArrayList<Hashtable<String,RDFNode>>();
	
	//@Test
	public void request(){	
		//Test OAContentRequest
		//OAContentByPersonRequest oaReq = new OAContentByPersonRequest();
		//ArrayList<Hashtable<String,RDFNode>> results = oaReq.performRequest(fname , lname);
		
		/*
		System.out.println(results.size());
	    for (int i=0 ; i< results.size() ; i++){
	    	Hashtable<String,RDFNode> soln = results.get(i); 
	    	
	    	// keywords
	    	//System.out.print(soln.get("publication") + ": ");
	    	System.out.print(soln.get("ptitle"));
	    	if(soln.containsKey("page")){
		    	System.out.println(": Page= " + soln.get("page"));
		    }
	    	if(soln.containsKey("urn")){
		    	System.out.println(": URN= " + soln.get("urn"));
		    }
	    	//if(soln.containsKey("work")){
		    //	System.out.println(": Work = " + soln.get("work"));
		    //}
	    	else{
	    		//System.out.println("");
	    	}
	    	
	      
	    }*/

		//Test OpenLibrary 
	    //OpenLibContentByPersonRequest olReq = new OpenLibContentByPersonRequest();
	    //ArrayList<Hashtable<String,RDFNode>> olResults = olReq.performRequest(fname , lname);
		//results.addAll(olResults);
		
		Thread sparqlOAThread = new Thread(new OARunable());
		sparqlOAThread.setName("OAExpl. Thread");
		sparqlOAThread.start();
		
		Thread sparqlOlThread = new Thread(new OlRunable());
		sparqlOlThread.setName("OpenLibrary Thread");
		sparqlOlThread.start();
		
		
		try {
			sparqlOlThread.join();
			sparqlOAThread.join();
		} catch (InterruptedException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		System.out.println(results.size());
	    for (int i=0 ; i< results.size() ; i++){
	    	Hashtable<String,RDFNode> soln = results.get(i); 
	    	
	    	// keywords
	    	System.out.print(i +1  + ". " + soln.get("ptitle"));
	    	if(soln.containsKey("page")){
		    	System.out.print(" : Page= " + soln.get("page"));
		    }
	    	else{
	    		System.out.print(" : Nachweis = " + soln.get("publication"));
	    	}
	    	if(soln.containsKey("urn")){
	    		System.out.print(": URN= " + soln.get("urn"));
	    	}
	    	if(soln.containsKey("isbn10")){
	    		System.out.print(": isbn10= " + soln.get("isbn10"));
	    	}
	    	if(soln.containsKey("isbn13")){
	    		System.out.print(": isbn13= " + soln.get("isbn13"));
	    	}
	    	System.out.println();
	    	
	      
	    }

	}
	
	public static void main(String[] args) {
		// TODO Auto-generated method stub

		TestSparqlEndpointQuery test = new TestSparqlEndpointQuery(); 
		test.request();
	}

	public class OARunable implements Runnable {

		@Override
		public void run() {
			//Test OA Explorer
			OAContentByPersonRequest oaReq = new OAContentByPersonRequest();
			ArrayList<Hashtable<String,RDFNode>> oaResults = oaReq.performRequest(fname , lname);
			results.addAll(oaResults);
			
			
		}
		
	}
	public class OlRunable implements Runnable {

		@Override
		public void run() {

			//Test OpenLibrary 
		    OpenLibContentByPersonRequest olReq = new OpenLibContentByPersonRequest();
		    ArrayList<Hashtable<String,RDFNode>> olResults = olReq.performRequest(fname , lname);
			results.addAll(olResults);
			
			
		}
		
	}
}
