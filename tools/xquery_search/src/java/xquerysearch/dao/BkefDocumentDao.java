package xquerysearch.dao;

import com.sleepycat.dbxml.XmlDocument;
import com.sleepycat.dbxml.XmlResults;

/**
 * DAO for BKEF document.
 * 
 * @author Tomas Marek
 *
 */
public interface BkefDocumentDao {

	public XmlDocument getDocumentById(String docId);
	
	public XmlResults query(String query);

	public boolean insertDocument(String docId, String docBody);
	
	public boolean removeDocument(String docId);
}
