package xquerysearch.dao.bdbxml;

import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.Document;
import xquerysearch.domain.Settings;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlException;

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
	public BdbxmlDocumentDao(Settings settings) {
		this.settings = settings;
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
	public Document getDocumentById(String docId) {
		XmlContainer cont = openConnection(settings.getContainerName());
		try {
			XmlDocument returnedDocument = cont.getDocument(docId);
			return new Document(returnedDocument.getName(), returnedDocument.getContentAsString());
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
	public boolean insertDocument(Document document) {
		XmlContainer cont = openConnection(settings.getContainerName());
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
		XmlContainer cont = openConnection(settings.getContainerName());
		try {
			cont.deleteDocument(docId);
			return true;
		} catch (XmlException e) {
			logger.warning("Removing document with id \"" + docId + "\" failed!");
			return false;
		}
	}
	
}
