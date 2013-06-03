package izi_repository.utils;

import izi_repository.domain.arbquery.QuerySettings;

/**
 * Utility class for {@link QuerySettings}.
 * 
 * @author Tomas Marek
 *
 */
public class QuerySettingsUtils {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private QuerySettingsUtils() {
	}
	
	/**
	 * Retrieves max results from query settings. Returns 0 if not found.
	 * 
	 * @param settings
	 * @return
	 */
	public static int getMaxResults(QuerySettings settings) {
		int maxResults = 0;
		
		if (settings != null) {
			maxResults = settings.getMaxResults();
		}
		
		return maxResults;
	}
}
