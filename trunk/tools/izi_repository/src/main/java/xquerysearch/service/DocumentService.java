package xquerysearch.service;

import xquerysearch.domain.Document;

/**
 * Service for {@link Document}.
 * 
 * @author Tomas Marek
 *
 */
public interface DocumentService {

	/**
	 * Inserts {@link Document}.
	 * 
	 * @param document
	 * @return <code>true</code> when successfully inserted, <code>false</code>
	 *         otherwise
	 */
	public boolean insertDocument(Document document);
	
	/**
	 * Retrieves {@link Document} by id.
	 * 
	 * @param docId
	 * @return found document
	 */
	public Document getDocumentById(String docId);
	
	/**
	 * Removes {@link Document} specified by id.
	 * 
	 * @param docId
	 * @return <code>true</code> when successfully removed, <code>false</code>
	 *         otherwise
	 */
	public boolean removeDocument(String docId);
}
