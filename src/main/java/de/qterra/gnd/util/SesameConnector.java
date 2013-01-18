/**
 * 
 */
package de.qterra.gnd.util;

import java.io.File;

import org.apache.log4j.Logger;
import org.openrdf.repository.Repository;
import org.openrdf.repository.RepositoryConnection;
import org.openrdf.repository.RepositoryException;
import org.openrdf.repository.sail.SailRepository;
import org.openrdf.sail.memory.MemoryStore;
import org.postgresql.Driver;

import com.hp.hpl.jena.db.DBConnection;

/**
 * Class Connector
 * 
 * <p><em>Title: Class for DB Connector properties</em></p>
 * <p>Description: The Class defines the connection properties required to 
 * access the DB backend used to persist the Jena triple store.</p>
 * 
 * @author aquast, email
 * creation date: 20.12.2010
 *
 */
public class SesameConnector {

	// Initiate Logger for MapperService
	private static Logger log = Logger.getLogger(SesameConnector.class);
	
	private static File repFile = new File("myRepository");
	private static MemoryStore memStore = new MemoryStore(repFile);
	
	/**
	 * @return
	 */
	public static RepositoryConnection createConnection(){

		Repository sesameRep = new SailRepository(memStore);
		RepositoryConnection connector = null;
		try {
			sesameRep.initialize();
			connector = sesameRep.getConnection();
		} catch (RepositoryException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return connector;
	}

	public static void setLog(Logger log) {
		SesameConnector.log = log;
	}

	public static void setRepfile(String dirName) {
		SesameConnector.repFile = new File(dirName);
	}


}
