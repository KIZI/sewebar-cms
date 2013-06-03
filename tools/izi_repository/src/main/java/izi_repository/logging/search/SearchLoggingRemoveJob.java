package izi_repository.logging.search;

import izi_repository.logging.event.EventLogger;

import java.io.File;

import org.quartz.JobExecutionContext;
import org.quartz.JobExecutionException;
import org.springframework.beans.factory.annotation.Autowired;
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

	@Autowired
	private EventLogger logger;

	/**
	 * {@inheritDoc}
	 */
	@Override
	public void executeInternal(JobExecutionContext arg0) throws JobExecutionException {
		logger.logInfo(this.getClass().toString(), "Job started");

		if (targetDirPath == null) {
			// TODO exclude from output?
			logger.logWarning(this.getClass().toString(), "Unable to remove old log files - no target folder specified!");
		} else {
			File targetDir = new File(targetDirPath);

			// TODO exclude from output?
			logger.logInfo(this.getClass().toString(), "Job will remove old log files from " + targetDir.getAbsolutePath());

			int removed = remover.removeLogs(keepMinutes, targetDir);

			// TODO exclude from output?
			logger.logInfo(this.getClass().toString(), "Job removed " + removed + " files older than " + keepMinutes + " minutes");
		}
		// TODO exclude from output?
		logger.logInfo(this.getClass().toString(), "Job finished");
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
