package xquerysearch.dao.bdbxml;

import xquerysearch.domain.Document;
import xquerysearch.domain.PmmlDocument;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;

/**
 * DAO for {@link PmmlDocument}, extends {@link BdbxmlDocumentDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlPmmlDocumentDao extends BdbxmlDocumentDao {

	/**
	 * @param settings
	 */
	public BdbxmlPmmlDocumentDao(SettingsManager settings) {
		super(settings);
	}

	/*
	 * @{InheritDoc}
	 */
	@Override
	public boolean insertDocument(Document document) {
		if ((document instanceof PmmlDocument) == false) {
			logger.warning("Document with id " + document.getDocId() +" has to be instance of PmmlDocument.");
			return false;
		}
		XmlContainer cont = openConnecion(settings.getContainerName());
		try {
			cont.putDocument(document.getDocId(), document.getDocBody());
			return true;
		} catch (XmlException e) {
			logger.warning("Inserting of document with id " + document.getDocId() + " failed - xml exception.");
			return false;
		} finally {
			closeConnection(cont);
		}
	}

}
