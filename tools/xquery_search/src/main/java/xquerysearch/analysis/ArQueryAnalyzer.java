package xquerysearch.analysis;

import java.util.HashMap;
import java.util.Map;

import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.ArQuery;

/**
 * Analyzes {@link ArQuery}.
 * 
 * @author Tomas Marek
 * 
 */
public class ArQueryAnalyzer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ArQueryAnalyzer() {
	}
	
	/**
	 * Analyzes {@link ArBuilderQuery}.
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
	 * @param query
	 * @return values describing {@link ArBuilderQuery}
	 */
	public static Map<String, Integer> analyze(ArQueryInternal aqi) {
		Map<String, Integer> ret = new HashMap<String, Integer>();

		ret.put("antecedentBbaCount", aqi.getAntecedentBbaSettingList().size());
		ret.put("consequentBbaCount", aqi.getConsequentBbaSettingList().size());
		ret.put("conditionBbaCount", aqi.getConditionBbaSettingList().size());
		return ret;
	}

}
