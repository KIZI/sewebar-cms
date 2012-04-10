package xquerysearch.dao.bdbxml;

import xquerysearch.dao.BkefDocumentDao;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;

/**
 * Implementation of {@link BkefDocumentDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlBkefDocumentDao extends ConnectionHelper implements BkefDocumentDao {
	
	/**
	 * Constructor
	 * @param settings 
	 */
	public BdbxmlBkefDocumentDao(SettingsManager settings) {
		this.settings = settings;
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
	public XmlDocument getDocumentById(String docId) {
		XmlContainer cont = openConnecion(settings.getContainerName());
		try {
			return cont.getDocument(docId);
		} catch (XmlException e) {
			logger.warning("Getting the document with id \"" + docId + "\" failed!");
			return null;
		} finally {
			closeConnection(cont);
		}
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
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

	/*
	 * @{InheritDoc}
	 */
	@Override
	public boolean insertDocument(String docId, String docBody) {
		XmlContainer cont = openConnecion(settings.getContainerName());
		try {
			cont.putDocument(docId, docBody);
			return true;
		} catch (XmlException e) {
			return false;
		} finally {
			closeConnection(cont);
		}
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
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

}
