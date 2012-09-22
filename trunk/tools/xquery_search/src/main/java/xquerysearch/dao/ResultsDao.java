package xquerysearch.dao;

import java.util.List;

import xquerysearch.domain.Query;
import xquerysearch.domain.Result;

/**
 * DAO for results returned by querying database.
 * 
 * @author Tomas Marek
 *
 */
public interface ResultsDao {

	/**
	 * Queries database for results.
	 * @param query
	 * @return list of {@link Result}s or <code>null</code> when no results were found
	 */
	public List<Result> getResultsByQuery(Query query);

	/**
	 * Queries database for results using XPath.
	 * @param xpath XPath query
	 * @return list of {@link Result}s or <code>null</code> when no results were found
	 */
	public List<Result> getResultsByXpath(String xpath);
}
