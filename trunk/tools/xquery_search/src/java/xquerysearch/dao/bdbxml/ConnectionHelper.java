package xquerysearch.dao.bdbxml;

import java.io.File;
import java.io.FileNotFoundException;
import java.util.logging.Logger;

import xquerysearch.controllers.MainController;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.db.DatabaseException;
import com.sleepycat.db.Environment;
import com.sleepycat.db.EnvironmentConfig;
import com.sleepycat.db.LockDetectMode;
import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlManager;
import com.sleepycat.dbxml.XmlManagerConfig;
import com.sleepycat.dbxml.XmlTransaction;

/**
 * Abstract class to help with database connection.
 * 
 * @author Tomas Marek
 *
 */
public abstract class ConnectionHelper {

	protected Logger logger = MainController.getLogger();
	protected SettingsManager settings;
	
	protected Environment environment;
	protected XmlManagerConfig xmlManagerConfig;
	protected XmlTransaction xmlTransaction;
	protected XmlManager xmlManager;
	
	private static final long CACHE_SIZE_MB = 128 * 1024 * 1024;
	private static final boolean RECOVER = false;
	
	/**
	 * 
	 * @return created environment or <code>null</code> when error occurs
	 */
	public Environment createEnvironment() {
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
	public XmlContainer openConnecion(String containerName) {
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
	public void closeConnection(XmlContainer xmlContainer) {
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
