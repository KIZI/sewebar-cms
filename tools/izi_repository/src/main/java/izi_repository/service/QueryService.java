package izi_repository.service;

import izi_repository.domain.Query;
import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.arbquery.hybridquery.ArHybridBuilderQuery;
import izi_repository.domain.arbquery.tasksetting.ArTsBuilderQuery;
import izi_repository.domain.result.Result;
import izi_repository.domain.result.ResultSet;

import java.util.List;


/**
 * Service for {@link Query}.
 * 
 * @author Tomas Marek
 * 
 */
public interface QueryService {

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
	 * @param settings
	 * @return {@link List} of {@link Result}s
	 */
	public List<Result> getResultList(ArBuilderQuery query, QuerySettings settings);

	/**
	 * Retrieves {@link Result}s as {@link List} from database by TaskSetting
	 * query.
	 * 
	 * @param query
	 * @param settings
	 * @return {@link List} of {@link Result}s
	 */
	public List<Result> getResultListByTsQuery(ArTsBuilderQuery query, QuerySettings settings);

	/**
	 * Retrieves {@link Result}s as {@link List} from database by hybrid query.
	 * 
	 * @param query
	 * @return {@link List} of {@link Result}s
	 */
	public List<Result> getResultListByHybridQuery(ArHybridBuilderQuery query);

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
