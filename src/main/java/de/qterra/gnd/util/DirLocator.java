/**
 * ConfDirLocator.java - This file is part of the DiPP Project by hbz
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
import java.util.Iterator;
import java.io.File;
import org.apache.log4j.Logger;

/**
 * Class ConfDirLocator
 * 
 * <p><em>Title: Locate the config dir and the temp dir for different Systems</em></p>
 * <p>Description: Class locates the Config File Location at different Environments.
 * It allows a seamless work on Servers and via Eclipse.</p>
 * 
 * @author Andres Quast, quast@hbz-nrw.de
 * creation date: 20.07.2009
 *
 */
public class DirLocator {

	// Initiate Logger for ConfDirLocator
	private static Logger log = Logger.getLogger(DirLocator.class);
	
	private static ArrayList<String> configDir = new ArrayList<String>();
	private static ArrayList<String> tempDir = new ArrayList<String>();
	
		
	private static void setConfLocations(){
		configDir.add(System.getProperty("user.dir") + "/conf/");
		configDir.add("/files/share/tomcat/webapps/dipp3/WEB-INF/");
		configDir.add(System.getProperty("user.home") + "/workspace/conf/dippConf/");
		configDir.add("/files/etc/dipp/");
	}
	
	private static void setTempLocations(){
		tempDir.add(System.getProperty("user.dir") + "/temp/");
		tempDir.add("/files/share/tomcat/webapps/temp/");
		tempDir.add(System.getProperty("user.home") + "/workspace/temp/");
		tempDir.add("/usr/share/tomcat6/webapps/temp/");
	}
	/**
	 * <p><em>Title:</em></p>
	 * <p>Description:  Runs to the ArrayList and check if one of the therein given Pathes exists. 
	 * If so assumes that this is the default Directory where config files are stored.</p>
	 * 
	 * @return 
	 */
	public static String getConfFileLocation(){

		setConfLocations();
		String configLocation = null;
		Iterator<String> dirName = configDir.iterator();
		while(dirName.hasNext()){
			String temp = dirName.next();
			if(new File(temp).isDirectory()){
				configLocation = temp;
			}
		}
		return configLocation;
	}		

	/**
	 * <p><em>Title:</em></p>
	 * <p>Description:  Runs to the ArrayList and check if one of the therein given Pathes exists. 
	 * If so assumes that this is the default Directory where config files are stored.</p>
	 * 
	 * @return 
	 */
	public static String getTempFileLocation(){

		setTempLocations();
		String tempLocation = null;
		Iterator<String> dirName = tempDir.iterator();
		while(dirName.hasNext()){
			String temp = dirName.next();
			if(new File(temp).isDirectory()){
				tempLocation = temp;
			}
		}
		return tempLocation;
	}		

}
