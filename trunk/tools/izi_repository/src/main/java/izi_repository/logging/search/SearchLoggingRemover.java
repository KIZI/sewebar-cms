package izi_repository.logging.search;

import java.io.File;

/**
 * Remover for search logger entries.
 * 
 * @author Tomas Marek
 *
 */
public class SearchLoggingRemover {
	
	private static final int KEEP_MINUTES_MIN = 5;

	public int removeLogs(long keepMinutes, File targetDir) {
		int removed = 0;

		if (keepMinutes < KEEP_MINUTES_MIN) {
			keepMinutes = KEEP_MINUTES_MIN;
		}

		if (targetDir.isDirectory()) {
			for (File file : targetDir.listFiles()) {
				if (file.lastModified() < (System.currentTimeMillis() - (keepMinutes * 60 * 1000))) {
					file.delete();
					removed++;
				}
			}
		}

		return removed;
	}
}
