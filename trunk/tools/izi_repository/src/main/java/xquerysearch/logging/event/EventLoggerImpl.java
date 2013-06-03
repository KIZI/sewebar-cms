package xquerysearch.logging.event;

import java.text.SimpleDateFormat;
import java.util.Calendar;

import org.apache.log4j.Logger;

/**
 * Default implementation of {@link EventLogger}.
 * 
 * @author Tomas Marek
 *
 */
public class EventLoggerImpl implements EventLogger {

	private static final Logger logger = Logger.getRootLogger();
	private StringBuilder fullLog = new StringBuilder();
	
	private static final SimpleDateFormat dateFormat = new SimpleDateFormat("dd-MM-yyyy HH:mm:ss.SSS");
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public void logDebug(String source, String debugMessage) {
		appendToLog("debug", debugMessage, source);
		logger.debug(source + ": " + debugMessage);
	}
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public void logInfo(String source, String info) {
		appendToLog("message", info, source);
		logger.info(source + ": " + info);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public void logWarning(String source, String warning) {
		appendToLog("warning", warning, source);
		logger.warn(source + ": " + warning);
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public void logError(String source, String error) {
		appendToLog("error", error, source);
		logger.error(source + ": " + error);
	}

	private void appendToLog(String element, String text, String source) {
		Calendar cal = Calendar.getInstance();
		fullLog.append("<" + element + " time=\"" + dateFormat.format(cal.getTime()) + "\" source=\"" + source + "\">");
		fullLog.append(text);
		fullLog.append("</" + element + ">");
	}
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public String getLog() {
		return "<log>" + fullLog.toString() + "</log>";
	}

}
