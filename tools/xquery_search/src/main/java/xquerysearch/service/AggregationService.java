package xquerysearch.service;

import java.util.List;

/**
 * Service for additional information.
 * 
 * @author Tomas Marek
 *
 */
public interface AggregationService {

	/**
	 * Returns names of all documents stored.
	 * 
	 * @return
	 */
	public List<String> getAllDocumentsNames();
	
	/**
	 * Returns all indexes in DB.
	 * 
	 * @return
	 */
	public List<String[]> getAllIndexes();
}
