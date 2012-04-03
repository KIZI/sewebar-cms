package xquerysearch.db;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.logging.Logger;

import xquerysearch.controllers.CommunicationManager;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.db.DatabaseException;
import com.sleepycat.db.Environment;
import com.sleepycat.db.EnvironmentConfig;
import com.sleepycat.db.LockDetectMode;
import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlIndexDeclaration;
import com.sleepycat.dbxml.XmlIndexSpecification;
import com.sleepycat.dbxml.XmlManager;
import com.sleepycat.dbxml.XmlManagerConfig;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;
import com.sleepycat.dbxml.XmlTransaction;

/**
 * 
 * @author Tomas Marek
 */

public class DbConnectionManager {
	
	private Logger logger;
	private SettingsManager settings;
	private Environment environment;
	private XmlManagerConfig xmlManagerConfig;
	private XmlTransaction xmlTransaction;
	private XmlManager xmlManager;
	private static final long CACHE_SIZE_MB = 128 * 1024 * 1024;
	private static final boolean RECOVER = false;
	private final String DATA_DESCRIPTION_CONTAINER = "__DataDescriptionCacheContainer";
	private final String DATA_DESCRIPTION_DOCUMENT = "__DataDescriptionCacheDocument";
	
	/**
	 * Constructor
	 * @param settings 
	 */
	public DbConnectionManager(SettingsManager settings) {
		logger = CommunicationManager.getLogger();
		this.settings = settings;
	}
	

	/**
	 * 
	 * @param query
	 * @return results or <code>null</code> when error occurs
	 */
	public XmlResults query(String query) {
		openConnecion(settings.getContainerName());
		try {
			XmlQueryContext queryContext = xmlManager.createQueryContext();
			return xmlManager.query(query, queryContext);
		} catch (XmlException e) {
			logger.warning("Query failed!");
			return null;
		} finally {
			closeConnection(null);
		}
	}
	
	/**
	 * 
	 * @param id
	 * @return xml document or <code>null</code> when error occurs
	 */
	public XmlDocument getDocumentById(String id) {
		XmlContainer cont = openConnecion(settings.getContainerName());
		try {
			return cont.getDocument(id);
		} catch (XmlException e) {
			logger.warning("Getting the document with id \"" + id + "\" failed!");
			return null;
		} finally {
			closeConnection(cont);
		}
	}
	
	/**
	 * Saves document into DB.
	 * @param document document body to save
	 * @param name document name
	 * @return if document is saved successfully returns <code>true</code> else returns <code>false</code>
	 */
	public boolean insertDocument(String document, String name) {
		XmlContainer cont = openConnecion(settings.getContainerName());
		try {
			cont.putDocument(name, document);
			return true;
		} catch (XmlException e) {
			return false;
		} finally {
			closeConnection(cont);
		}			
	}
	
	/**
	 * Removes document from DB
	 * @param docId document identification
	 * @return if successful returns <code>true</code>, else <code>false</code>
	 */
	public boolean removeDocument(String docId) {
		XmlContainer cont = openConnecion(settings.getContainerName());
		try {
			cont.deleteDocument(docId);
			return true;
		} catch (XmlException e) {
			logger.warning("Removing document with id \"" + docId + "\" failed!");
			return false;
		}
	}
	
	/**
	 * Adds index into DB
	 * @param index index to add
	 * @return <code>true</code> when successful, otherwise <code>false</code>
	 */
	public boolean addIndex(String index) {
        XmlContainer cont = openConnecion(settings.getContainerName());
		String[] indexSplit = index.split(";");
		XmlIndexSpecification indexSpec = null;
		try {
			if (indexSplit.length == 3) {
                indexSpec = cont.getIndexSpecification();
                indexSpec.addIndex(indexSplit[0], indexSplit[1], indexSplit[2]);
                cont.setIndexSpecification(indexSpec);
                return true;
			} else {
				logger.warning("Adding index \"" + index + "\" failed! - Not 3 params");
				return false;
			}
    	} catch (XmlException e) {
    		logger.warning("Adding index \"" + index + "\" failed! - Xml exception");
    		return false;
    	} finally {
    		indexSpec.delete();
    		closeConnection(cont);
    	}
	}
	
	/**
	 * Removes index entry form DB
	 * @param index index to remove
	 * @return <code>true</code> when successfully removed, <code>false</code> otherwise 
	 */
	public boolean removeIndex(String index) {
		XmlContainer cont = openConnecion(settings.getContainerName());
        String[] indexSplit = index.split(";");
        XmlIndexSpecification indexSpec = null;
        try {
            if (indexSplit.length == 3) {
        		indexSpec = cont.getIndexSpecification();
                indexSpec.deleteIndex(indexSplit[0], indexSplit[1], indexSplit[2]);
                cont.setIndexSpecification(indexSpec);
                return true;
            } else {
            	logger.warning("Removing index \"" + index + "\" failed! - Not 3 params");
            	return false;
            }
        } catch (XmlException e) {
        	logger.warning("Removing index \"" + index + "\" failed! - Xml exception");
        	return false;
        } finally {
        	indexSpec.delete();
        	closeConnection(cont);
        }
	}
	
	/**
	 * Lists indexes used in DB
	 * @return if error occurs <code>null</code> else list of indexes 
	 */
	public String listIndexes() {
		XmlContainer cont = openConnecion(settings.getContainerName());
		XmlIndexSpecification indexSpec = null;
		try {
            indexSpec = cont.getIndexSpecification();
            String indexes = "";
            
            int count = 0;
            XmlIndexDeclaration indexDeclaration = null;
            while ((indexDeclaration = (indexSpec.next())) != null) {
                indexes += "<index>"
                                + "<nodeName>" + indexDeclaration.name + "</nodeName>"
                                + "<indexType>" + indexDeclaration.index + "</indexType>"
                            + "</index>";
                count++;
            }
            String indexesCount = "<indexCount>" + count + "</indexCount>";
            return indexesCount + indexes;
		} catch (XmlException e) {
			logger.warning("Listing indexes failed! - Xml exeption");
			return null;
		} finally {
			indexSpec.delete();
			closeConnection(cont);
		}
	}
	
	/**
	 * Gets data description from DB
	 * @return data description or <code>null</code> when error occurs
	 */
	public String getDataDescription() {
		XmlContainer cont = openConnecion(DATA_DESCRIPTION_CONTAINER);
		try {
    		XmlDocument doc = cont.getDocument(DATA_DESCRIPTION_DOCUMENT);
    		return doc.getContentAsString();
		} catch (XmlException e) {
			logger.warning("Getting data description failed!");
			return null;
		} finally {
			closeConnection(cont);
		}
	}
	
	/**
	 * Saves data description into DB
	 * @param dataDescription data description to save
	 * @return <code>true</code> when successful, <code>false</code> when error occurs
	 */
	public boolean saveDataDescription(String dataDescription) {
		XmlContainer cont = openConnecion(DATA_DESCRIPTION_CONTAINER);
		try {
			cont.putDocument(DATA_DESCRIPTION_DOCUMENT, dataDescription);
			return true;
		} catch (XmlException e) {
			logger.warning("Saving data description failed!");
			return false;
		} finally {
			closeConnection(cont);
		}
	}
	
	/**
	 * 
	 * @return created environment or <code>null</code> when error occurs
	 */
	private Environment createEnvironment() {
		EnvironmentConfig config = new EnvironmentConfig();
		config.setTransactional(true);
		config.setAllowCreate(true);
		config.setInitializeCache(true);
		config.setRunRecovery(RECOVER);
		config.setCacheSize(CACHE_SIZE_MB);
		config.setInitializeLocking(true);
		config.setInitializeLogging(true);
		config.setErrorStream(System.err);
		config.setLockDetectMode(LockDetectMode.MINWRITE);
		config.setLogAutoRemove(true);
		config.setLockTimeout(3);
		config.setLogAutoRemove(true);
		File f = new File(settings.getEnvironmentDirectory());
		try {
			return new Environment(f, config);
		} catch (FileNotFoundException e) {
			logger.severe("Environment home directory not found!");
			return null;
		} catch (DatabaseException e) {
			logger.severe("Database error!");
			return null;
		}
	}

	/**
	 * Creates connection with Berkeley XML DB
	 * @return created <code>XmlContainer</code> or <code>null</code> when error occurs
	 */
	private XmlContainer openConnecion(String containerName) {
		try {
			environment = createEnvironment();
			xmlManagerConfig = new XmlManagerConfig();
			//xmlManagerConfig.setAllowExternalAccess(true);
			xmlManager = new XmlManager(environment, xmlManagerConfig);
			xmlTransaction = xmlManager.createTransaction();
			XmlContainer xmlContainer = xmlManager.openContainer(containerName);
			return xmlContainer;
		} catch (XmlException e) {
			logger.severe("Connection with DB cannot be created!");
			return null;
		}
	}
	
	/**
	 * Closes the DBXML connection, deletes xml container
	 * @param xmlContainer xml container to delete
	 */
	private void closeConnection(XmlContainer xmlContainer) {
		commit();
		if (xmlContainer != null) {
			xmlContainer.delete();
		}
		if (xmlManager != null) {
			xmlManager.delete();
		}
	}
	
	/**
	 * Commits changes into XML DB
	 */
	private void commit() {
		try {
			xmlTransaction.commit();
		} catch (XmlException e) {
			logger.severe("DB commit failed!");
		}
	}
}
