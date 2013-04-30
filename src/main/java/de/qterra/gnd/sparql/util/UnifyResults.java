/**
 * UnifyResults.java - This file is part of the DiPP Project by hbz
 * Library Service Center North Rhine Westfalia, Cologne 
 *
 * -----------------------------------------------------------------------------
 *
 * <p><b>License and Copyright: </b>The contents of this file are subject to the
 * D-FSL License Version 1.0 (the "License"); you may not use this file
 * except in compliance with the License. You may obtain a copy of the License
 * at <a href="http://www.dipp.nrw.de/dfsl/">http://www.dipp.nrw.de/dfsl/.</a></p>
 *
 * <p>Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.</p>
 *
 * <p>Portions created for the Fedora Repository System are Copyright &copy; 2002-2005
 * by The Rector and Visitors of the University of Virginia and Cornell
 * University. All rights reserved."</p>
 *
 * -----------------------------------------------------------------------------
 *
 */
package de.qterra.gnd.sparql.util;

import java.util.ArrayList;
import java.util.Enumeration;
import java.util.Hashtable;

import org.apache.log4j.Logger;

import com.hp.hpl.jena.rdf.model.RDFNode;

/**
 * Class UnifyResults
 * 
 * <p><em>Title: </em></p>
 * <p>Description: </p>
 * 
 * @author aquast, email
 * creation date: 20.03.2012
 *
 */
public class UnifyResults {

	// Initiate Logger for UnifyResults
	private static Logger log = Logger.getLogger(UnifyResults.class);
	
	private ArrayList<Hashtable<String,RDFNode>> results = null;
	private ArrayList<String> comparator = null;
	private Hashtable<String, ArrayList<String>> unifyHash = null;
	private ArrayList<String> relatedNodes = null;
	
	
	public ArrayList<Hashtable<String,ArrayList<String>>> unify(){
		ArrayList<Hashtable<String,ArrayList<String>>> 
		
		unifiedResults = new ArrayList<Hashtable<String,ArrayList<String>>>();

 
		for(int j=0; j<comparator.size(); j++){
			String comp = comparator.get(j);
		   	unifyHash = new Hashtable<String, ArrayList<String>>();

		    for (int i=0 ; i< results.size() ; i++){
			    String compKey = null;
		    	Hashtable<String,RDFNode> soln = results.get(i); 
	    		relatedNodes = new ArrayList<String>();
			    
		    	// determinate if any key in results equals any key in Comparators
	    		// if so, use key for the next step, if not add Node to Array
	    		Enumeration<String> kEnum = soln.keys();
		    	while (kEnum.hasMoreElements()){
		    		String key = kEnum.nextElement();
		    		if(key.equals(comp)){
						compKey = soln.get(key).toString();
						log.debug("neuer CompKey: " + compKey);
		    		}else{
		    			relatedNodes.add(soln.get(key).toString());
		    			log.debug("Node ohne CompKey");
		    		}
		    	}
		    	
		    	//Test if any key equals any compKey
		    	// if so test if we already have any Hash with the comparator node
		    	if(compKey == null){
		    		log.error("comparing Key wasn't found in result");
		    	}else{
		    		log.debug("compKey: " + compKey);
		    		if(unifyHash.containsKey(compKey)){
			    		unifyHash.get(compKey).addAll(relatedNodes);
			    		log.debug("compKey already exists");
		    		}else{
		    			unifyHash.put(compKey, relatedNodes);
		    			log.debug("new node has following relations: " );
		    			for (int k = 0; k < relatedNodes.size(); k++){
		    				
		    			}
		    		}
		    	}
		    	
	    	}
	    	unifiedResults.add(unifyHash);
		}
		return unifiedResults;
	}
	

	public ArrayList<Hashtable<String, ResourceResponse>> unify(String compKey){
		
		// provide Result as ArrayList of Hashtables. 
		// outer Hashtable Key (String) is the Value found for CompKey, 
		// inner Hashtable Key is holds the key for any the String[] in ArrayList
		// unlike outer key this is not the value but the key of any column found in SPARQL result
		
		//ResourceResponse rResponse = new ResourceResponse();
		
		ArrayList<Hashtable<String, ResourceResponse>> unifiedResults = 
		new ArrayList<Hashtable<String, ResourceResponse>>();

		Hashtable<String, ResourceResponse> unifiedColValues = new Hashtable<String, ResourceResponse>();
	    for (int i=0 ; i< results.size() ; i++){
	    	
	    	// assume any result has provides an Value for the compKey
	    	// TODO verify: is that assumption correct?
	    	String compValue = results.get(i).get(compKey).toString();

	    	// a result with the compValue is already existing
	    	if(compValue  != null && unifiedColValues.containsKey(compValue)){
	    		
	    		Enumeration<String> keyEnum = results.get(i).keys();
	    		while(keyEnum.hasMoreElements()){
	    			String key = keyEnum.nextElement();
	    			String value = results.get(i).get(key).toString();
	    			//log.info(value);
	    			if(unifiedColValues.get(compValue).getResponse().containsKey(key)){
	    				if(!unifiedColValues.get(compValue).getResponse().get(key).contains(value)){
		    				unifiedColValues.get(compValue).getResponse().get(key).add(value);
			    			}
	    			}else{
	    				ArrayList<String> values  = new ArrayList<String>();
	    				//values.add(results.get(i).get(key).toString());
	    				//colValues.put(key, values);
	    				unifiedColValues.get(compValue).getResponse().put(key, values);
	    			}
	    			
	    		}

	    	}else {
	    		//we have a new result
	    		ResourceResponse colValues = new ResourceResponse();
 	    		
	    		//find all cols (as Hashtable keys) provided with this result
	    		Enumeration<String> keyEnum = results.get(i).keys();
	    		while(keyEnum.hasMoreElements()){
	    			String key = keyEnum.nextElement();
	       			ArrayList<String> values = new ArrayList<String>();
	    			values.add(results.get(i).get(key).toString());
	    			colValues.getResponse().put(key, values);
	    			log.debug("added first: " + key + " : " + results.get(i).get(key).toString());
	    		}
	    		// we add a new result
	    		unifiedColValues.put(compValue, colValues);
				unifiedResults.add(unifiedColValues);
	    	}
	    		
		}
	    return unifiedResults;
	}

	public void setResults(ArrayList<Hashtable<String,RDFNode>> Results){
		results = Results;
	}
	
	public void setComparator(ArrayList<String> Comparator){
		comparator = Comparator;
	}

}
