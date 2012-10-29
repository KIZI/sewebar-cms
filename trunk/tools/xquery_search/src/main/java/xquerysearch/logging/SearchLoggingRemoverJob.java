package xquerysearch.logging;

import java.io.File;
import java.util.Calendar;

import org.apache.log4j.Logger;
import org.quartz.JobExecutionContext;
import org.quartz.JobExecutionException;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.scheduling.quartz.QuartzJobBean;

/**
 * Job for log files removal.
 * 
 * @author Tomas Marek
 * 
 */
public class SearchLoggingRemoverJob extends QuartzJobBean {

	@Value("${logging.target.dir}")
	private String targetDirPath;

	@Value("${logging.remove.keep.seconds:300}")
	private long keepSeconds;

	Logger logger = Logger.getLogger(getClass());

	/**
	 * {@inheritDoc}
	 */
	@Override
	public void executeInternal(JobExecutionContext arg0) throws JobExecutionException {
		logger.info("Search log removal job started at " + Calendar.getInstance().getTime());

		int removed = SearchLoggingRemover.removeLogs(keepSeconds, new File(targetDirPath));

		logger.info("Search log removal job removed " + removed + "files older than " + keepSeconds
				+ " seconds");
	}

}
