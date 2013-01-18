/**
 * 
 */
package de.qterra.gnd.triplestore;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.InputStream;
import java.util.Iterator;

import org.postgresql.Driver;

import com.hp.hpl.jena.db.DBConnection;
import com.hp.hpl.jena.rdf.model.*;
import com.hp.hpl.jena.query.*;

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.util.Connector;


/**
 * @author aquast
 *
 */
public class RdfXmlReader {

	private static File rdfXmlFile = new File("/home/aquast/downloader/PNDrdf_2.out.xml");
	private static Model gnd = ModelFactory.createDefaultModel();
	private ModelMaker conModel;
	
	public void createDBConnectedModel(){
		
		try {
			
			Class jdbc = Class.forName("org.postgresql.Driver");
			System.out.println(Driver.getVersion());
			DBConnection connection = Connector.createConnection();
			conModel = ModelFactory.createModelRDBMaker(connection);
			gnd = conModel.createModel("NormDatei", false) ;
			
			gnd.begin();
			
			InputStream in =  new FileInputStream(rdfXmlFile);
			gnd.read(in, null, "RDF/XML");
			
			gnd.commit();
			
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
	}

	/**
	 * A method for reading RDF Statements provided as N-Triples 
	 * into a Jena Model
	 */
	public void readTriples(){
		InputStream triplesStream = null;
		
		try {
			triplesStream = new FileInputStream(rdfXmlFile);
		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		
		gnd.read(triplesStream, null, "RDF/XML");
	}
	
	/**
	 * A very special method to find the URI of PND 
	 * for a Person by the give surname. Thought for testing 
	 * purposes for the moment 
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
	 * @param args
	 */
	public static void main(String[] args) {
		// TODO Auto-generated method stub
		RdfXmlReader reader = new RdfXmlReader();
		//reader.readTriples();
		reader.createDBConnectedModel();
		//reader.findTriples();
		//reader.queryBySparql();
		//gnd.write(System.out);
	}

}
