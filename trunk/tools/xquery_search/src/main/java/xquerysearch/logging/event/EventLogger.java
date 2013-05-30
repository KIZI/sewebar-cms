package xquerysearch.logging.event;

/**
 * Logger for logging events in application.<br />
 * Logged events:
 * <ul>
 * 		<li>errors</li>
 * 		<li>messages</li>
 * 		<li>warnings</li>
 * </ul>
 * 
 * @author Tomas Marek
 *
 */
public interface EventLogger {

	/**
	 * Method for logging debugging messages - use only for development
	 * 
	 * @param source
	 * @param debugMessage
	 */
	public void logDebug(String source, String debugMessage);
	
	/**
	 * Method for logging info messages - lowest log level
	 * 
	 * @param source
	 * @param info
	 */
	public void logInfo(String source, String info);
	
	/**
	 * Method for logging warnings - middle log level
	 * 
	 * @param source
	 * @param warning
	 */
	public void logWarning(String source, String warning);
	
	/**
	 * Method for logging error - highest log level
	 * 
	 * @param source
	 * @param error
	 */
	public void logError(String source, String error);
	
	/**
	 * Return full log
	 * 
	 * @return
	 */
	public String getLog();
}
