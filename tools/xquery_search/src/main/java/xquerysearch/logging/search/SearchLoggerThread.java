package xquerysearch.logging.search;

import java.io.File;
import java.io.FileWriter;
import java.io.IOException;

import org.springframework.beans.factory.annotation.Autowired;

import xquerysearch.logging.event.EventLogger;

/**
 * Class saving given message into newly created log file.
 * 
 * @author Tomas Marek
 * 
 */
public class SearchLoggerThread implements Runnable {

	@Autowired
	private EventLogger logger;

	private String message;
	private File logFile;

	/**
	 * 
	 */
	public SearchLoggerThread(String message, File logFile) {
		this.message = message;
		this.logFile = logFile;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public void run() {
		if (logFile == null || message == null) {
			// TODO exclude from output?
			logger.logWarning(this.getClass().toString(), "Unable to save search log - file or message missing!");
		} else {
			try {
				FileWriter fw = new FileWriter(logFile);
				fw.write(message);
				fw.flush();
				fw.close();

				// TODO exclude from output?
				logger.logInfo(this.getClass().toString(), "New Search log created: " + logFile.getAbsolutePath());
			} catch (IOException e) {
				// TODO exclude from output?
				logger.logWarning(this.getClass().toString(), "Unable to save search log - IOException!");
			}
		}

	}

}
