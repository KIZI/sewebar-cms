package izi_repository.service;

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
	
	/**
	 * Returns amount of all documents stored in DB.
	 * 
	 * @return
	 */
	public Long getDocumentsCount();
	
	/**
	 * Returns amount of all Association Rules stored in DB.
	 * 
	 * @return
	 */
	public Long getAssociationRulesCount();
}
