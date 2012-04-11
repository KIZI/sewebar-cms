package xquerysearch.dao.bdbxml;

import xquerysearch.domain.BkefDocument;
import xquerysearch.domain.Document;
import xquerysearch.settings.SettingsManager;

import com.sleepycat.dbxml.XmlContainer;
import com.sleepycat.dbxml.XmlException;

/**
 * DAO for {@link BkefDocument}, extends {@link BdbxmlDocumentDao}.
 * 
 * @author Tomas Marek
 *
 */
public class BdbxmlBkefDocumentDao extends BdbxmlDocumentDao {

	/**
	 * @param settings
	 */
	public BdbxmlBkefDocumentDao(SettingsManager settings) {
		super(settings);
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

}
