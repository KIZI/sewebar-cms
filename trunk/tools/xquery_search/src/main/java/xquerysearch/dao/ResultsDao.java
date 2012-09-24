package xquerysearch.dao;

import java.util.List;

import xquerysearch.domain.Query;
import xquerysearch.domain.result.ResultSet;

/**
 * DAO for results returned by querying database.
 * 
 * @author Tomas Marek
 * 
 */
public interface ResultsDao {

	/**
	 * Queries database for result. Maps results to {@link ResultSet}.
	 * 
	 * @param query
	 * @return {@link ResultSet} or <code>null</code> when no results were found
	 */
	public ResultSet getResultSetByQuery(Query query);

	/**
	 * Queries database for result using XPath. Maps results to
	 * {@link ResultSet}.
	 * 
	 * @param xpath
	 *            XPath query
	 * @return {@link ResultSet} or <code>null</code> when no results were found
	 */
	public ResultSet getResultSetByXpath(String xpath);

	/**
	 * Queries database for results.
	 * 
	 * @param query
	 * @return list of {@link Result}s or <code>null</code> when no results were
	 *         found
	 */
	public List<String> getResultsByQuery(Query query);

	/**
	 * Queries database for results using XPath.
	 * 
	 * @param xpath
	 *            XPath query
	 * @return list of {@link Result}s or <code>null</code> when no results were
	 *         found
	 */
	public List<String> getResultsByXpath(String xpath);
}
