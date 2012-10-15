package xquerysearch.service;

import java.util.List;

import xquerysearch.domain.Query;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;

/**
 * Service for {@link Query}.
 * 
 * @author Tomas Marek
 * 
 */
public interface QueryService {

	/**
	 * Gets results from database by query.
	 * 
	 * @param query
	 * @return {@link ResultSet} including found results
	 */
	public ResultSet getResultSet(Query query);

	/**
	 * Gets {@link ResultSet} from database by XPath query as String.
	 * 
	 * @param xpath
	 * @param maxResults
	 * @return {@link ResultSet} including found results
	 */
	public ResultSet getResultSet(String xpath, int maxResults);

	/**
	 * Retrieves {@link Result}s as {@link List} from database by query.
	 * 
	 * @param query
	 * @return {@link List} of {@link Result}s
	 */
	public List<Result> getResultList(String query);

	/**
	 * Queries database with given query and returns single result value as
	 * {@link String}.
	 * 
	 * @param query
	 * @return single value or <tt>null</tt> if 0 of > 1 results returned from
	 *         DB
	 */
	public String queryForSingleValue(String query);

	/**
	 * Queries database with given query, returns results as {@link String}.
	 * 
	 * @param query
	 * @return
	 */
	public String query(String query);
}
