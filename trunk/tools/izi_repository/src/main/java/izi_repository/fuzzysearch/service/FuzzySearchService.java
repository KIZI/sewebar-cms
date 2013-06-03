package izi_repository.fuzzysearch.service;

import izi_repository.domain.arbquery.ArBuilderQuery;
import izi_repository.domain.arbquery.QuerySettings;
import izi_repository.domain.arbquery.tasksetting.ArTsBuilderQuery;
import izi_repository.domain.result.Result;

import java.util.List;


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
