package xquerysearch.utils;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.ArQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.arbquery.tasksetting.ArTsQuery;

/**
 * Utilities for querying.
 * 
 * @author Tomas Marek
 * 
 */

public class QueryUtils {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private QueryUtils() {
	}

	/**
	 * Helps retrieve {@link QuerySettings} from {@link ArBuilderQuery}.
	 * 
	 * @param query
	 * @return
	 */
	public static QuerySettings getQuerySettings(ArBuilderQuery query) {
		if (query != null) {
			return getQuerySettings(query.getArQuery());
		}
		return null;
	}

	/**
	 * Helps retrieve {@link QuerySettings} from {@link ArTsBuilderQuery}.
	 * 
	 * @param query
	 * @return
	 */
	public static QuerySettings getQuerySettings(ArTsBuilderQuery query) {
		if (query != null) {
			return getQuerySettings(query.getArTsQuery());
		}
		return null;
	}

	/**
	 * Helps retrieve {@link QuerySettings} from {@link ArTsQuery}.
	 * 
	 * @param query
	 * @return
	 */
	public static QuerySettings getQuerySettings(ArTsQuery query) {
		if (query != null) {
			return query.getQuerySettings();
		}
		return null;
	}

	/**
	 * Helps retrieve {@link QuerySettings} from {@link ArQuery}.
	 * 
	 * @param query
	 * @return
	 */
	public static QuerySettings getQuerySettings(ArQuery query) {
		if (query != null) {
			return query.getQuerySettings();
		}
		return null;
	}

	/**
	 * Provides removal of XML file declaration and oxygen declaration.
	 * 
	 * @param queryBody
	 *            query body to process
	 * @return processed query body when successful, otherwise <code>null</code>
	 */
	public static String deleteDeclaration(String queryBody) {
		String output = "";
		String splitXMLBegin[] = queryBody.split("([<][?][x][m][l])|([<][?][o][x][y][g][e][n])");
		if (splitXMLBegin.length == 1) {
			output = queryBody;
		} else {
			for (int i = 0; i <= (splitXMLBegin.length - 1); i++) {
				if (i == 0) {
					output += splitXMLBegin[i];
				} else {
					String splitXMLEnd[] = splitXMLBegin[i].split("[?][>]");
					if (splitXMLEnd.length > 1) {
						String splitXMLBack = splitXMLEnd[1];
						output += splitXMLBack;
					}
				}
			}
		}
		return output;
	}

}
