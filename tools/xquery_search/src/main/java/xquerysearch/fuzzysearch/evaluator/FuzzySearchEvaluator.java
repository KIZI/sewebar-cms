package xquerysearch.fuzzysearch.evaluator;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.ArTsQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.TaskSettingInternal;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.tasksetting.TaskSetting;

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
	 *         should be 100 if the result is completely the same as the given
	 *         query. Otherwise the value should be < 100.
	 */
	public double[][] evaluate(AssociationRuleInternal ari, ArQueryInternal aqi);
	
	/**
	 * Evaluates found {@link TaskSetting}s represented by {@link TaskSettingInternal}.
	 * 
	 * @param tsi
	 * @param atqi
	 * @return Value describing how much is result similar to query. This value
	 *         should be 100 if the result is completely the same as the given
	 *         query. Otherwise the value should be < 100.
	 */
	public double[][] evaluate(TaskSettingInternal tsi, ArTsQueryInternal atqi);
}
