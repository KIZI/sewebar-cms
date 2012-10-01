package xquerysearch.fuzzysearch.evaluator;

import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.result.Result;

/**
 * Evaluator used for fuzzy searching.
 * 
 * @author Tomas Marek
 * 
 */
public interface FuzzySearchEvaluator {

	/**
	 * Evaluates searching {@link Result}.
	 * 
	 * @param result
	 * @param arBuilderQuery
	 * @return Value describing how much is result similar to query. This value
	 *         should be 1 if the result is completely the same as the given
	 *         query. Otherwise the value should be < 1.
	 */
	public Double evaluate(Result result, ArBuilderQuery arBuilderQuery);
}
