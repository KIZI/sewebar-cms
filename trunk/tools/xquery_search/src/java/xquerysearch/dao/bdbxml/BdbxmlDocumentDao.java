package xquerysearch.dao.bdbxml;

import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.BkefDocument;
import xquerysearch.domain.Document;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;
import com.sleepycat.dbxml.XmlQueryContext;
import com.sleepycat.dbxml.XmlResults;

/**
 * Implementation of {@link DocumentDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlDocumentDao extends ConnectionHelper implements DocumentDao {

	/**
	 * Constructor
	 * @param settings 
	 */
	public BdbxmlDocumentDao(SettingsManager settings) {
		this.settings = settings;
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
	public Document getDocumentById(String docId) {
		XmlContainer cont = openConnecion(settings.getContainerName());
		try {
			XmlDocument returnedDocument = cont.getDocument(docId);
			return new BkefDocument(returnedDocument.getName(), returnedDocument.getContentAsString());
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
	public boolean insertDocument(Document document) {
		XmlContainer cont = openConnecion(settings.getContainerName());
		try {
			cont.putDocument(document.getDocId(), document.getDocBody());
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
