package xquerysearch.logging;

/**
 * Logger providing logging of obtained queries and their results.
 * 
 * @author Tomas Marek
 * 
 */
public interface SearchLogger {

	/**
	 * Logs obtained query.
	 * 
	 * @param query
	 * @param timestamp
	 */
	public void logQuery(String query, long timestamp);

	/**
	 * Logs obtained results.
	 * 
	 * @param result
	 * @param timestamp
	 */
	public void logResult(String result, long timestamp);
}
