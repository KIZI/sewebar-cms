package xquerysearch.logging;

import java.io.File;

/**
 * Remover for search logger entries.
 * 
 * @author Tomas Marek
 *
 */
public class SearchLoggingRemover {

	/**
	 * 
	 */
	private SearchLoggingRemover() {
	}
	
	public static int removeLogs(long keepSeconds, File targetDir) {
		int removed = 0;
		
		if (keepSeconds < 60) {
			keepSeconds = 60;
		}
		
		if (targetDir.isDirectory()) {
			for (File file : targetDir.listFiles()) {
				if (file.lastModified() < (System.currentTimeMillis() - (keepSeconds * 1000))) {
					file.delete();
					removed++;
				}
			}
		}
		
		return removed;
	}
}
