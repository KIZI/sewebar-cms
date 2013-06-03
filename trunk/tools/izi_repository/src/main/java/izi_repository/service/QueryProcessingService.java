package izi_repository.service;

/**
 * Service for processing obtained query.
 * 
 * @author Tomas Marek
 *
 */
public interface QueryProcessingService {

	/**
	 * Processes obtained query as {@link String}.
	 * 
	 * @param query
	 * @param startTime
	 * @return result of querying
	 */
	public String processQuery(String query, long startTime);
	
	/**
	 * Processes directly obtained query - no further processing of query. 
	 * 
	 * @param query
	 * @return
	 */
	public String processDirectQuery(String query);
}
