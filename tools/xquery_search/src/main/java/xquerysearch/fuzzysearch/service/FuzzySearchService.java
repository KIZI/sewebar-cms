package xquerysearch.fuzzysearch.service;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.ResultSet;

/**
 * Service providing fuzzy search.
 * 
 * @author Tomas Marek
 * 
 */
public interface FuzzySearchService {

	/**
	 * Evaluates {@link ResultSet} and sets the value retrieved from evaluator
	 * to all {@link Result}s in {@link ResultSet}.
	 * 
	 * @param resultSet
	 * @return evaluated {@link ResultSet}.
	 */
	public ResultSet evaluateResultSet(ResultSet resultSet, ArBuilderQuery query);

}
