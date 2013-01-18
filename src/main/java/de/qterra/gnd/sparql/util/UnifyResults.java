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
		ArrayList<Hashtable<String,ArrayList<String>>> unifiedResults = new ArrayList<Hashtable<String,ArrayList<String>>>();

 
		for(int j=0; j<comparator.size(); j++){
			String comp = comparator.get(j);
		   	unifyHash = new Hashtable<String, ArrayList<String>>();

		    for (int i=0 ; i< results.size() ; i++){
			    String compKey = null;
		    	Hashtable<String,RDFNode> soln = results.get(i); 
	    		relatedNodes = new ArrayList<String>();
			    
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
		    	
		    	//Test if we already have any Hash with the comparator node
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
	
	
	public void setResults(ArrayList<Hashtable<String,RDFNode>> Results){
		results = Results;
	}
	
	public void setComparator(ArrayList<String> Comparator){
		comparator = Comparator;
	}

}
