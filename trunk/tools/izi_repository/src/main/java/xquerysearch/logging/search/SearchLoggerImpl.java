package xquerysearch.logging.search;

import java.io.File;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.beans.factory.annotation.Value;

import xquerysearch.logging.event.EventLogger;

/**
 * Implementation of {@link SearchLogger}.
 * 
 * @author Tomas Marek
 * 
 */
public class SearchLoggerImpl implements SearchLogger {

	@Autowired
	private EventLogger logger;

	@Value("${logging.target.dir}")
	private String targetDirPath;

	@Value("${logging.fileextension.query:xml}")
	private String queryFileExtension;

	@Value("${logging.fileextension.result:xml}")
	private String resultFileExtension;

	@Value("${logging.filenameappend.query:-query}")
	private String queryFilenameAppend;

	@Value("${logging.filenameappend.result:-result}")
	private String resultFilenameAppend;

	/**
	 * {@inheritDoc}
	 */
	@Override
	public void logQuery(String query, long timestamp) {
		if (targetDirPath == null) {
			// TODO exclude from output?
			logger.logWarning(this.getClass().toString(), "Search logging - no target directory specified!");
		} else {
			if (timestamp == 0) {
				timestamp = System.currentTimeMillis();
			}

			File targetFile = new File(targetDirPath + "\\" + timestamp + queryFilenameAppend + "."
					+ queryFileExtension);

			SearchLoggerThread thread = new SearchLoggerThread(query, targetFile);
			new Thread(thread).start();
		}
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public void logResult(String result, long timestamp) {
		if (targetDirPath == null) {
			// TODO exclude from output?
			logger.logWarning(this.getClass().toString(), "Search logging - no target directory specified!");
		} else {
			if (timestamp == 0) {
				timestamp = System.currentTimeMillis();
			}

			File targetFile = new File(targetDirPath + "\\" + timestamp + resultFilenameAppend + "."
					+ resultFileExtension);

			SearchLoggerThread thread = new SearchLoggerThread(result, targetFile);
			new Thread(thread).start();
		}
	}

}
