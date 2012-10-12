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
	 * Gets {@link ResultSet} from database by query as String.
	 * 
	 * @param query
	 * @return {@link ResultSet} including found results
	 */
	public ResultSet getResultSet(String query);

	/**
	 * Gets {@link Result}s from database by query and sorts them.
	 * 
	 * @param query
	 * @return {@link List} of sorted {@link Result}s
	 */
	public List<Result> getSortedResults(String query);

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
