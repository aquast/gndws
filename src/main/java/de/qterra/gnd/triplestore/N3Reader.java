/**
 * 
 */
package de.qterra.gnd.triplestore;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.InputStream;
import java.util.Calendar;
import java.util.Iterator;

import org.openjena.atlas.logging.Log;
import org.postgresql.Driver;

import com.hp.hpl.jena.db.DBConnection;
import com.hp.hpl.jena.rdf.listeners.StatementListener;
import com.hp.hpl.jena.rdf.model.*;
import com.hp.hpl.jena.query.*;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.util.Connector;


/**
 * @author aquast
 * Class for ingesting N3 Triples of gGND into the Jena Triplestore
 */
public class N3Reader {

	private static File triplesFile = null;
	private String path = "/home/aquast/Download/example.rdf/example.n3";
	private static Model gnd = ModelFactory.createDefaultModel();
	private ModelMaker conModel;
	
	/**
	 * <p><em>Title: Method creates a persistent Jena Model, uses postgresql</em></p>
	 * <p>Description: Method reads in N3Triples and ingests them into a persistent Jena Model</p>
	 *  
	 */
	public void createDBConnectedModel(){
		
		try {
			
			Class jdbc = Class.forName("org.postgresql.Driver");
			System.out.println(Driver.getVersion());
			DBConnection connection = Connector.createConnection();
			conModel = ModelFactory.createModelRDBMaker(connection);
			gnd = conModel.createModel("NormDatei", false) ;
			
			//gnd.begin();
			
			triplesFile = new File(path);
			InputStream in =  new FileInputStream(triplesFile);
			BufferedInputStream bi = new BufferedInputStream(in);
			gnd.read(bi, null, "N-TRIPLES");
			
			gnd.commit();
			
			
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}
	
	/**
	 * <p><em>Title: </em></p>
	 * <p>Description: Method to write N3-Triples from a splittes File into Database, 
	 * only needed if Memory or Disk-Space Problem occur</p>
	 *  
	 */
	public void createDBConnectedModelBySplittedFile(){
		//TODO: implement this method
		
		//triplesFile = new File(path +"00");
		//createDBConnectedModel();
		
		boolean next = true;
		int i = 0;
					
		while(next){	
			if (i < 10){
				triplesFile = new File(path + "0" + i);
			}
			else{
				triplesFile = new File(path + i);	
			}
			
			if(triplesFile.isFile()){
				Calendar cal = Calendar.getInstance();
				System.out.println("Verarbeite Datei " + triplesFile.getName() + " um " 
						+ cal.get(Calendar.HOUR_OF_DAY) + ":" + cal.get(Calendar.MINUTE) + "\n");	
				createDBConnectedModel();
				long lo = 120000;
				try {
					Thread.sleep(lo);
				} catch (InterruptedException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
			else{
				System.out.println("Verarbeitung beendet");
				next = false;
			}
			i++;
		}
		

	}

	/**
	 * A method for reading RDF Statements provided as N-Triples 
	 * into a Jena Model. This Method does NOT create a persistent model in
	 * any Database
	 */
	public void readTriples(){
		InputStream triplesStream = null;
		
		try {
			triplesStream = new FileInputStream(triplesFile);
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		gnd.read(triplesStream, null, "N-TRIPLES");
	}
	
	/**
	 * A method to find the URI of PND 
	 * for a Person by the give surname. For testing 
	 * purposes only 
	 */
	public void findTriples(){
		//Property prop = null;
		DBConnection connection = Connector.createConnection();
		conModel = ModelFactory.createModelRDBMaker(connection);
		
		gnd = conModel.openModel("NormDatei", true);
		Property prop = gnd.getProperty("http://d-nb.info/gnd/surname");
		Property propPrefName = gnd.getProperty("http://d-nb.info/gnd/variantNameForThePerson"); 
		Iterator resit = null;
		StmtIterator it = gnd.listStatements(new SimpleSelector(null, prop, "Dyk"));
		while (it.hasNext()){
			
			Resource resnew = it.nextStatement().getSubject();

			/*ResIterator resIt = gnd.listSubjectsWithProperty(propPrefName, resnew);
			while(resIt.hasNext()){
				System.out.println(resIt.nextResource().getURI());
				System.out.println("oops");
			}*/
			
			StmtIterator secIt = gnd.listStatements(new SimpleSelector(null, null, resnew));
			while(secIt.hasNext()){
				System.out.println(secIt.nextStatement().getSubject().getURI());
			}
		}
	}
	
	/**
	 * <p><em>Title: Method to Query Jena Triplestore by SparQL</em></p>
	 * <p>Description: For testing purposes only</p>
	 *  
	 */
	public void queryBySparql(){

		DBConnection connection = Connector.createConnection();
		conModel = ModelFactory.createModelRDBMaker(connection);
		
		gnd = conModel.openModel("NormDatei", true);
		String queryString = "select ?uri ?name where \n" +
				" {?uri <http://RDVocab.info/ElementsGr2/dateOfBirth> \"1801\" .\n" +
				" ?uri <http://d-nb.info/gnd/preferredNameForThePerson> ?name}";
		Query query = QueryFactory.create(queryString);
		QueryExecution qexec = QueryExecutionFactory.create(query, gnd);
		try {
		    ResultSet results = qexec.execSelect();
		    for ( ; results.hasNext() ; )
		    {
		      QuerySolution soln = results.nextSolution() ;
		      RDFNode x = soln.get("?name") ; // Get a result variable by name.
			  System.out.print(x);
		      RDFNode y = soln.get("?uri") ;       // Get a result variable by name.
			  System.out.println("; " + y);
		    }
		  } finally { qexec.close() ; }
	}
	
	/**
	 * <p><em>Title: Main Class, provides access to Methods in the N3Reader.Class </em></p>
	 * <p>Description: uncomment the methods for using them</p>
	 * 
	 * @param args 
	 */
	public static void main(String[] args) {
		
		N3Reader reader = new N3Reader();
		//reader.readTriples();
		reader.createDBConnectedModel();
		//reader.createDBConnectedModelBySplittedFile();
		//reader.findTriples();
		//reader.queryBySparql();
		//gnd.write(System.out);
	}

}
