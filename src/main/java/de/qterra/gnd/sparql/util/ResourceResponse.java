package de.qterra.gnd.sparql.util;

import java.util.ArrayList;
import java.util.Hashtable;

import org.apache.log4j.Logger;

public class ResourceResponse {

	// Initiate Logger for ResourceResponse
	private static Logger log = Logger.getLogger(ResourceResponse.class);
	private Hashtable<String,ArrayList<String>> response = new Hashtable<String,ArrayList<String>>();
	
	public Hashtable<String, ArrayList<String>> getResponse() {
		return response;
	}
	
	public void setResponse(Hashtable<String, ArrayList<String>> response) {
		this.response = response;
	}
}
