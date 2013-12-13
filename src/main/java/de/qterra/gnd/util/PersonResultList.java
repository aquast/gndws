/**
 * PersonResultList.java - This file is part of the DiPP Project by hbz
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
package de.qterra.gnd.util;

import java.util.ArrayList;

import javax.xml.bind.annotation.XmlRootElement;

import org.apache.log4j.Logger;

/**
 * Class PersonResultList
 * 
 * <p><em>Title: </em></p>
 * <p>Description: </p>
 * 
 * @author aquast, email
 * creation date: 13.12.2013
 *
 */
@XmlRootElement
public class PersonResultList {

	// Initiate Logger for PersonResultList
	private static Logger log = Logger.getLogger(PersonResultList.class);


	/**
	 * 
	 */
	public PersonResultList() {

	}
	

	private ArrayList<PersonResult> personResultList = new ArrayList<PersonResult>();
	private String resultSize = null;


	public ArrayList<PersonResult> getPersonResultList() {
		return personResultList;
	}


	public void setPersonResultList(ArrayList<PersonResult> personResultList) {
		this.personResultList = personResultList;
	}

	public void addPersonResultList(ArrayList<PersonResult> personResultList) {
		this.personResultList.addAll(personResultList);
	}

	public void addPersonResult(PersonResult personResult) {
		this.personResultList.add(personResult);
	}

	public String getResultSize() {
		return resultSize;
	}


	public void setResultSize(String resultSize) {
		this.resultSize = resultSize;
	}

}
