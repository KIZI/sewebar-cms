package xquerysearch.logging;

import java.io.File;

import org.apache.log4j.Logger;
import org.quartz.JobExecutionContext;
import org.quartz.JobExecutionException;
import org.springframework.scheduling.quartz.QuartzJobBean;

/**
 * Job for log files removal.
 * 
 * @author Tomas Marek
 * 
 */
public class SearchLoggingRemoveJob extends QuartzJobBean {

	private SearchLoggingRemover remover;
	private String targetDirPath;
	private long keepMinutes;

	Logger logger = Logger.getLogger(getClass());

	/**
	 * {@inheritDoc}
	 */
	@Override
	public void executeInternal(JobExecutionContext arg0) throws JobExecutionException {
		logger.info("Job started");

		if (targetDirPath == null) {
			logger.warn("Unable to remove old log files - no target folder specified!");
		} else {
			File targetDir = new File(targetDirPath);

			logger.info("Job will remove old log files from " + targetDir.getAbsolutePath());

			int removed = remover.removeLogs(keepMinutes, targetDir);

			logger.info("Job removed " + removed + " files older than " + keepMinutes + " minutes");
		}
		logger.info("Job finished");
	}

	/**
	 * @param remover
	 *            the remover to set
	 */
	public void setRemover(SearchLoggingRemover remover) {
		this.remover = remover;
	}

	/**
	 * @param keepMinutes
	 *            the keepMinutes to set
	 */
	public void setKeepMinutes(long keepMinutes) {
		this.keepMinutes = keepMinutes;
	}

	/**
	 * @param targetDirPath
	 *            the targetDirPath to set
	 */
	public void setTargetDirPath(String targetDirPath) {
		this.targetDirPath = targetDirPath;
	}
}
