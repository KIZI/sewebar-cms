package xquerysearch.analysis;

import java.util.HashMap;
import java.util.Map;

import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.result.Result;

/**
 * Analyzes {@link Result}.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultAnalyzer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ResultAnalyzer() {
	}

	/**
	 * Analyzes {@link Result} represented by {@link AssociationRuleInternal}.
	 * <br />
	 * <br />
	 * Available values:
	 * <ul>
	 * 		<li>antecedentBbaCount</li>
	 * 		<li>consequentBbaCount</li>
	 * 		<li>conditionBbaCount</li>
	 * 		<li></li>
	 * </ul>
	 * 
	 * @param result
	 * @return values describing {@link Result}
	 */
	public static Map<String, Integer> analyze(AssociationRuleInternal ari) {
		Map<String, Integer> ret = new HashMap<String, Integer>();
		ret.put("antecedentBbaCount", ari.getAntecedentBbas().size());
		ret.put("consequentBbaCount", ari.getConsequentBbas().size());
		ret.put("conditionBbaCount", ari.getConditionBbas().size());
		return ret;
	}

}
