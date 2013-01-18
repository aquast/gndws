/**
 * 
 */
package de.qterra.gnd.util;

import org.apache.log4j.Logger;
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
public class Connector {

	// Initiate Logger for MapperService
	private static Logger log = Logger.getLogger(Connector.class);
	
	private static String jdbcUri = "jdbc:postgresql://localhost/";
	private static String dbName = "gnddb";
	private static String dbType = "PostgreSQL";
	private static String driverClassName = "org.postgresql.Driver";
	private static String user = "gnduser";
	private static String passwd = "gndAdmin";
	
	/**
	 * @return
	 */
	public static DBConnection createConnection(){
		try {
			Class jdbc = Class.forName(driverClassName);
		} catch (ClassNotFoundException e) {
			log.error(e);
		}
		log.info("Use JDBC-Driver: " + Driver.getVersion());
		DBConnection connection = new DBConnection(jdbcUri + dbName, user , passwd, dbType);
		return connection;
	}

	public static void setLog(Logger log) {
		Connector.log = log;
	}

	public static void setJdbcUri(String jdbcUri) {
		Connector.jdbcUri = jdbcUri;
	}

	public static void setDbName(String dbName) {
		Connector.dbName = dbName;
	}

	public static void setDbType(String dbType) {
		Connector.dbType = dbType;
	}

	public static void setDriverClassName(String driverClassName) {
		Connector.driverClassName = driverClassName;
	}

	public static void setUser(String user) {
		Connector.user = user;
	}

	public static void setPasswd(String passwd) {
		Connector.passwd = passwd;
	}

}
