package xquerysearch.fuzzysearch.evaluator;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.result.Result;

/**
 * Evaluator used for fuzzy searching.
 * 
 * @author Tomas Marek
 * 
 */
public interface FuzzySearchEvaluator {

	/**
	 * Evaluates found {@link Result}s represented by {@link AssociationRuleInternal}.
	 * 
	 * @param ari AR internal
	 * @param aqi ArQuery internal
	 * @return Value describing how much is result similar to query. This value
	 *         should be 1 if the result is completely the same as the given
	 *         query. Otherwise the value should be < 1.
	 */
	public Double[][] evaluate(AssociationRuleInternal ari, ArQueryInternal aqi);
}
