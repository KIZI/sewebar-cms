package izi_repository.dao;

import izi_repository.domain.Document;

/**
 * DAO for {@link Document}.
 * 
 * @author Tomas Marek
 *
 */
public interface DocumentDao {
	
	/**
	 * Retrieves {@link Document} from database.
	 * @param docId ID of document to find
	 * @return {@link Document} when found, otherwise <code>null</code>.
	 */
	public Document getDocumentById(String docId);
	
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
