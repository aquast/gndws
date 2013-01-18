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

import de.qterra.gnd.sparql.SparqlQuery;
import de.qterra.gnd.util.Connector;

import org.openrdf.model.Resource;
import org.openrdf.repository.Repository;
import org.openrdf.repository.RepositoryConnection;
import org.openrdf.repository.sail.SailRepository;
import org.openrdf.rio.RDFFormat;
import org.openrdf.sail.memory.MemoryStore;



/**
 * @author aquast
 * Class for ingesting N3 Triples of gGND into the Jena Triplestore
 */
public class N3Reader4Sesame {

	private static File triplesFile = null;
	private String path = "/home/aquast/Download/example.rdf/example.n3";
	
	/**
	 * <p><em>Title: Method creates a model stored in Sesame TripleStore </em></p>
	 * <p>Description: Method reads in Turtle-Triples and ingests them into a Model</p>
	 *  
	 */
	public void createSesameModel(){
		
		try {
			File dataDir = new File("myRepository");
			Repository sesameRep = new SailRepository(new MemoryStore(dataDir));
			sesameRep.initialize();
			
			RepositoryConnection connector = sesameRep.getConnection();
			
			try{
				triplesFile = new File(path);
				//Resource[] res = null; 
				InputStream in =  new FileInputStream(triplesFile);
				BufferedInputStream bi = new BufferedInputStream(in);
				connector.add(bi, "http://www.q-terra.de", RDFFormat.N3);				
			}catch (Exception e){
				
			}finally{
				connector.close();
			}
			
			
		} catch (Exception e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} 
	}
	

	
	/**
	 * <p><em>Title: Main Class, provides access to Methods in the N3Reader.Class </em></p>
	 * <p>Description: uncomment the methods for using them</p>
	 * 
	 * @param args 
	 */
	public static void main(String[] args) {
		
		N3Reader4Sesame reader = new N3Reader4Sesame();
		//reader.readTriples();
		reader.createSesameModel();
		//reader.createDBConnectedModelBySplittedFile();
		//reader.findTriples();
		//reader.queryBySparql();
		//gnd.write(System.out);
	}

}
