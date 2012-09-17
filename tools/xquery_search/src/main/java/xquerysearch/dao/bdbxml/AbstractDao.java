package xquerysearch.dao.bdbxml;

import java.util.logging.Logger;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;

import xquerysearch.controller.MainController;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlManager;
import com.sleepycat.dbxml.XmlTransaction;

/**
 * Abstract class to help with database connection.
 * 
 * @author Tomas Marek
 *
 */
public abstract class AbstractDao {

	protected Logger logger = MainController.getLogger();
	
	@Value("${container.name}")
	protected String containerName;
	
	@Autowired
	protected XmlManager xmlManager;
	/**
	 * Closes the DBXML connection by committing, deletes XML container
	 * @param xmlContainer
	 * @param xmlTransaction
	 */
	public void commitAndClose(XmlTransaction xmlTransaction, XmlContainer xmlContainer) {
		if (xmlContainer != null) {
			xmlContainer.delete();
		}
		try {
			xmlTransaction.commit();
		} catch (XmlException e) {
			logger.severe("Could not commit and close transaction!");
		} catch (NullPointerException e) {
			logger.severe("Could not commit and close transaction!");
		}
	}

}
