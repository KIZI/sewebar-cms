package xquerysearch.dao;

import xquerysearch.domain.Document;

import com.sleepycat.dbxml.XmlResults;

/**
 * DAO for {@link Document}.
 * 
 * @author Tomas Marek
 *
 */
public interface DocumentDao {
	
	/**
	 * Retrieves {@link Document} from database.
	 * @param docId if of document to found
	 * @return {@link Document} when found, otherwise <code>null</code>.
	 */
	public Document getDocumentById(String docId);
	
	public XmlResults query(String query);

	/**
	 * Saves {@link Document} into database;
	 * @param document {@link Document} to save
	 * @return Returns <code>true</code> when successful, otherwise returns <code>false</code>.
	 */
	public boolean insertDocument(Document document);
	
	/**
	 * Removes {@link Document} from database.
	 * @param docId id of document to remove
	 * @return Returns <code>true</code> when successful, otherwise <code>false</code>.
	 */
	public boolean removeDocument(String docId);

}