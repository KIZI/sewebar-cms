package xquerysearch.service;

import xquerysearch.dao.DocumentDao;
import xquerysearch.domain.Document;

/**
 * Service for operations with {@link Document}s.
 * 
 * @author Tomas Marek
 *
 */
public class DocumentService {
	
	DocumentDao dao;
	
	public Document getDocumentById(String docId) {
		return dao.getDocumentById(docId); 
	}
	
	public boolean insertDocument(Document document) {
		return dao.insertDocument(document);
	}
	
	public boolean removeDocument(String docId) {
		return dao.removeDocument(docId);
	}

}
