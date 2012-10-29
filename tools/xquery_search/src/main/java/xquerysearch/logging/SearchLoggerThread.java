package xquerysearch.logging;

import java.io.File;
import java.io.FileWriter;
import java.io.IOException;

import org.apache.log4j.Logger;

/**
 * Class saving given message into newly created log file.
 * 
 * @author Tomas Marek
 * 
 */
public class SearchLoggerThread implements Runnable {

	Logger logger = Logger.getLogger(getClass());

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
			logger.warn("Unable to save search log - file or message missing!");
		} else {
			try {
				FileWriter fw = new FileWriter(logFile);
				fw.write(message);
				fw.flush();
				fw.close();

				logger.info("New Search log created: " + logFile.getAbsolutePath());
			} catch (IOException e) {
				logger.warn("Unable to save search log - IOException!");
			}
		}

	}

}
