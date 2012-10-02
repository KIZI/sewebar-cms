package xquerysearch.analysis;

import java.util.HashMap;
import java.util.Map;
import java.util.Set;

import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.Cedent;
import xquerysearch.domain.result.DBA;
import xquerysearch.domain.result.Result;
import xquerysearch.domain.result.Rule;

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
	 * Analyzes {@link Result}.
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
	public static Map<String, Integer> analyze(Result result) {
		Map<String, Integer> ret = new HashMap<String, Integer>();
		Rule rule = result.getRule();
		Cedent antecedent = null;
		Cedent consequent = null;
		Cedent condition = null;
		if (rule != null) {
			antecedent = rule.getAntecedent();
			consequent = rule.getConsequent();
			condition = rule.getCondition();
		}
		ret.put("antecedentBbaCount", getBbaCountForCedent(antecedent));
		ret.put("consequentBbaCount", getBbaCountForCedent(consequent));
		ret.put("conditionBbaCount", getBbaCountForCedent(condition));
		return ret;
	}

	/**
	 * Gets count of {@link BBA}s for {@link Cedent}.
	 * 
	 * @param cedent
	 * @return
	 */
	private static int getBbaCountForCedent(Cedent cedent) {
		if (cedent == null) {
			return 0;
		}

		int bbaCount = 0;
		for (DBA dba : cedent.getDbas()) {
			bbaCount += getBbaCountForDba(dba);
		}
		return bbaCount;
	}

	/**
	 * Gets count of {@link BBA}s for {@link DBA}.
	 * 
	 * @param dba
	 * @return
	 */
	private static int getBbaCountForDba(DBA dba) {
		if (dba == null) {
			return 0;
		}

		int count = 0;
		Set<BBA> bbas = dba.getBbas();
		if (bbas != null) {
			count += bbas.size();
		} else {
			Set<DBA> dbas = dba.getDbas();
			if (dbas != null) {
				for (DBA loopDba : dbas) {
					count += getBbaCountForDba(loopDba);
				}
			}
		}
		return count;
	}
}
