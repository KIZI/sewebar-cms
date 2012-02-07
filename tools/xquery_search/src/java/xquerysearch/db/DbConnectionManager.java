package xquerysearch.db;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.logging.Logger;

import xquerysearch.CommunicationManager;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.db.DatabaseException;
import com.sleepycat.db.Environment;
import com.sleepycat.db.EnvironmentConfig;
import com.sleepycat.db.LockDetectMode;
import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;
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
	
	/**
	 * Constructor
	 * @param settings 
	 */
	public DbConnectionManager(SettingsManager settings) {
		logger = CommunicationManager.logger;
		this.settings = settings;
	}
	

	/**
	 * 
	 * @param query
	 * @return results or <code>null</code> when error occurs
	 */
	public XmlResults query(String query) {
		openConnecion();
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
		XmlContainer cont = openConnecion();
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
		XmlContainer cont = openConnecion();
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
	private XmlContainer openConnecion() {
		try {
			environment = createEnvironment();
			xmlManagerConfig = new XmlManagerConfig();
			//xmlManagerConfig.setAllowExternalAccess(true);
			xmlManager = new XmlManager(environment, xmlManagerConfig);
			xmlTransaction = xmlManager.createTransaction();
			XmlContainer xmlContainer = xmlManager.openContainer(settings.getContainerName());
			return xmlContainer;
		} catch (XmlException e) {
			logger.severe("Connection with DB cannot be created!");
			return null;
		}
	}
	
	/**
	 * 
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
	
	private void commit() {
		try {
			xmlTransaction.commit();
		} catch (XmlException e) {
			CommunicationManager.logger.severe("DB commit failed!");
		}
	}
}
