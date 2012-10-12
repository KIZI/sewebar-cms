package xquerysearch.fuzzysearch.evaluator;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import xquerysearch.analysis.ArQueryAnalyzer;
import xquerysearch.analysis.ResultAnalyzer;
import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.arbquery.ArBuilderQuery;
import xquerysearch.domain.arbquery.BbaSetting;
import xquerysearch.domain.arbquery.BbaSettings;
import xquerysearch.domain.result.BBA;

/**
 * Implementation of {@link FuzzySearchEvaluator}.
 * 
 * @author Tomas Marek
 * 
 */
public class FuzzySearchEvaluatorImpl implements FuzzySearchEvaluator {

	private static final Double DEFAULT_COMPLIANCE = 100.0;

	/**
	 * @{inheritDoc
	 */
	@Override
	public Double evaluate(AssociationRuleInternal ari, ArQueryInternal aqi) {
		Double resultCompliance = DEFAULT_COMPLIANCE;

		Map<String, Integer> resultAnalysis = ResultAnalyzer.analyze(ari);
		// TODO move one level up? - performance
		Map<String, Integer> queryAnalysis = ArQueryAnalyzer.analyze(aqi);

		Double bbaCountPenalty = checkBbaCounts(resultAnalysis, queryAnalysis);
		if (bbaCountPenalty != null) {
			resultCompliance -= checkBbaCounts(resultAnalysis, queryAnalysis);
		}
		
		System.out.println(evaluateBbas(ari.getAntecedentBbas(), aqi.getConsequentBbaSettingList()));
		
		return resultCompliance;
	}

	/**
	 * TODO documentation
	 * 
	 * @return
	 */
	private static double checkBbaCounts(Map<String, Integer> resultAnalysis,
			Map<String, Integer> queryAnalysis) {

		double[] valuesPairAntecedent = getValuesPair("antecedentBbaCount", "antecedentBbaCount",
				resultAnalysis, queryAnalysis);

		double[] valuesPairConsequent = getValuesPair("consequentBbaCount", "consequentBbaCount",
				resultAnalysis, queryAnalysis);

		double[] valuesPairCondition = getValuesPair("conditionBbaCount", "conditionBbaCount",
				resultAnalysis, queryAnalysis);

		double antecedentPenalty = 0.0;
		double consequentPenalty = 0.0;
		double conditionPenalty = 0.0;
		if (valuesPairAntecedent != null) {
			antecedentPenalty = Math.abs(valuesPairAntecedent[0] - valuesPairAntecedent[1]) * 2;
		}
		if (valuesPairConsequent != null) {
			consequentPenalty = Math.abs(valuesPairConsequent[0] - valuesPairConsequent[1]) * 2;
		}
		if (valuesPairCondition != null) {
			conditionPenalty = Math.abs(valuesPairCondition[0] - valuesPairCondition[1]) * 2;
		}
		return antecedentPenalty + consequentPenalty + conditionPenalty;
	}

	private static double[] evaluateBbas(List<BBA> bbas, List<BbaSetting> bbaSettings) {
		double ret[] = new double[bbaSettings.size()];

		return ret;
	}

	private static double evaluateBba(BBA bba, BbaSetting bbaSetting) {

		return 0.0;
	}

	/**
	 * TODO documentation
	 * 
	 * @return
	 */
	private static double[] getValuesPair(String resultMapId, String queryMapId,
			Map<String, Integer> resultAnalysis, Map<String, Integer> queryAnalysis) {
		if (resultMapId == null || resultMapId.isEmpty()) {
			return null;
		}
		if (queryMapId == null || queryMapId.isEmpty()) {
			return null;
		}
		if (queryAnalysis == null || resultAnalysis == null) {
			return null;
		}

		Integer resultValue = resultAnalysis.get(resultMapId);
		Integer queryValue = queryAnalysis.get(queryMapId);

		if (resultValue == null || queryValue == null) {
			return null;
		}

		return new double[] { resultValue, queryValue };
	}

}
