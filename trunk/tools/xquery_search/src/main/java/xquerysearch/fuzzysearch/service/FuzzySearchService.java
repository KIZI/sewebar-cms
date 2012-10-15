package xquerysearch.fuzzysearch.service;

import java.util.Set;

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
	 * Evaluates {@link Set} of {@link Result}s and sets the value retrieved from evaluator
	 * to all {@link Result}s.
	 * 
	 * @param results
	 * @return evaluated {@link Set} of {@link Result}s
	 */
	public Set<Result> evaluateResults(Set<Result> results, ArBuilderQuery query);

}
