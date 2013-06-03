package xquerysearch.fuzzysearch.service;

import java.util.List;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.QuerySettings;
import xquerysearch.domain.arbquery.tasksetting.ArTsBuilderQuery;
import xquerysearch.domain.result.Result;

/**
 * Service providing fuzzy search.
 * 
 * @author Tomas Marek
 * 
 */
public interface FuzzySearchService {

	/**
	 * Returns {@link List} of fuzzy evaluated {@link Result}s by {@link ArBuilderQuery}. 
	 * 
	 * @param query
	 * @param settings
	 * @return
	 */
	public List<Result> getFuzzyResultsByQuery(ArBuilderQuery query, QuerySettings settings);

	/**
	 * Returns {@link List} of fuzzy evaluated {@link Result}s by {@link ArTsBuilderQuery}.  
	 * 
	 * @param query
	 * @param settings
	 * @return
	 */
	public List<Result> getFuzzyResultsByQuery(ArTsBuilderQuery query, QuerySettings settings);
}
