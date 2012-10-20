package xquerysearch.fuzzysearch.evaluator;

import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import org.springframework.beans.factory.annotation.Value;

import xquerysearch.analysis.ArQueryAnalyzer;
import xquerysearch.analysis.ResultAnalyzer;
import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.arbquery.BbaSetting;
import xquerysearch.domain.result.BBA;

/**
 * Implementation of {@link FuzzySearchEvaluator}.
 * 
 * @author Tomas Marek
 * 
 */
public class FuzzySearchEvaluatorImpl implements FuzzySearchEvaluator {

	private static final Double DEFAULT_COMPLIANCE = 100.0;

	private static int bbaCountPenalty;

	private static int missingCategoryPenalty;

	private static int leftoverCategoryPenalty;

	/**
	 * @{inheritDoc
	 */
	@Override
	public Double[][] evaluate(AssociationRuleInternal ari, ArQueryInternal aqi) {
		Double resultCompliance = DEFAULT_COMPLIANCE;

		Map<String, Integer> resultAnalysis = ResultAnalyzer.analyze(ari);
		// TODO move one level up? - performance
		Map<String, Integer> queryAnalysis = ArQueryAnalyzer.analyze(aqi);

		Double bbaCountPenalty = checkBbaCounts(resultAnalysis, queryAnalysis);
		if (bbaCountPenalty != null) {
			resultCompliance -= checkBbaCounts(resultAnalysis, queryAnalysis);
		}

		Double[] antecedentBbaVector = evaluateBbas(ari.getAntecedentBbas(), aqi.getAntecedentBbaSettingList());
		Double[] consequentBbaVector = evaluateBbas(ari.getConsequentBbas(), aqi.getConsequentBbaSettingList());

		return new Double[][]{antecedentBbaVector, consequentBbaVector, new Double[]{resultCompliance}};
	}

	/**
	 * Evaluates counts of BBAs.
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
			antecedentPenalty = Math.abs(valuesPairAntecedent[0] - valuesPairAntecedent[1]) * bbaCountPenalty;
		}
		if (valuesPairConsequent != null) {
			consequentPenalty = Math.abs(valuesPairConsequent[0] - valuesPairConsequent[1]) * bbaCountPenalty;
		}
		if (valuesPairCondition != null) {
			conditionPenalty = Math.abs(valuesPairCondition[0] - valuesPairCondition[1]) * bbaCountPenalty;
		}
		return antecedentPenalty + consequentPenalty + conditionPenalty;
	}

	/**
	 * Evaluates compliance of list of BBA's from found AR to list of
	 * BbaSettings from ArQuery.
	 * 
	 * @param bbas
	 * @param bbaSettings
	 * @return
	 */
	private static Double[] evaluateBbas(List<BBA> bbas, List<BbaSetting> bbaSettings) {
		Double[] ret = new Double[bbaSettings.size()];

		for (int i = 0; i < bbaSettings.size(); i++) {
			BbaSetting bbaSetting = bbaSettings.get(i);
			BBA bba = getCorrespondingBba(bbaSetting.getFieldRef().getValue(), bbas);
			ret[i] = evaluateBba(bba, bbaSetting);
		}

		return ret;
	}

	/**
	 * Evaluates single BBA - how much BBA from found AR complies to BbaSetting
	 * from ArQuery.
	 * 
	 * @param bba
	 * @param bbaSetting
	 * @return
	 */
	private static double evaluateBba(BBA bba, BbaSetting bbaSetting) {
		if (bba != null && bbaSetting != null) {
			return evaluatesCategories(bba.getTransformationDictionary().getCatNames(), bbaSetting
					.getCoefficient().getCategories());
		}
		return 0.0;
	}

	/**
	 * Retrieves matching BBA from list of found AR's BBAs to ArQuery
	 * BbaSetting. Field Name (e.g. District) is used to find match.
	 * 
	 * @param fieldRef
	 * @param bbas
	 * @return
	 */
	private static BBA getCorrespondingBba(String fieldRef, List<BBA> bbas) {
		if (fieldRef != null && bbas != null) {
			for (BBA bba : bbas) {
				if (bba.getTransformationDictionary().getFieldName().equals(fieldRef)) {
					return bba;
				}
			}
		}
		return null;
	}

	/**
	 * Evaluates compliance of categories from found AR and from ArQuery.
	 * 
	 * @param bbaCategories
	 * @param bbaSettingCategories
	 * @return
	 */
	private static double evaluatesCategories(Set<String> bbaCategories, Set<String> bbaSettingCategories) {
		double compliance = DEFAULT_COMPLIANCE;
		if (bbaCategories != null && bbaSettingCategories != null) {
			for (String bbaSetCategory : bbaSettingCategories) {
				if (hasCorrespondingCategory(bbaSetCategory, bbaCategories) == false) {
					compliance -= missingCategoryPenalty;
				}
				int leftovers = getLeftoverCategoriesCount(bbaSettingCategories, bbaCategories);
				compliance -= (leftovers * leftoverCategoryPenalty);
			}
		}
		return compliance;
	}

	/**
	 * Retrieves matching category of found Association Rule to category from
	 * ArQuery.
	 * 
	 * @param category
	 * @param bbaCategories
	 * @return
	 */
	private static boolean hasCorrespondingCategory(String category, Set<String> bbaCategories) {
		for (String loopCategory : bbaCategories) {
			if (loopCategory.equals(category)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Gets count of categories that are in found Association Rule but not in
	 * ArQuery.
	 * 
	 * @param bbaSetCategories
	 * @param bbaCategories
	 * @return
	 */
	private static int getLeftoverCategoriesCount(Set<String> bbaSetCategories, Set<String> bbaCategories) {
		Set<String> bbaCategoriesTemp = new HashSet<String>(bbaCategories);
		for (String bbaSetCategory : bbaSetCategories) {
			bbaCategoriesTemp.remove(bbaSetCategory);
		}
		return bbaCategoriesTemp.size();
	}

	/**
	 * Retrieves values pair for variable from found AR and from ArQuery.
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

	/**
	 * @param bbaCountPenalty
	 *            the bbaCountPenalty to set
	 */
	@SuppressWarnings("static-access")
	@Value("${penalty.bbaCount}")
	public void setBbaCountPenalty(int bbaCountPenalty) {
		this.bbaCountPenalty = bbaCountPenalty;
	}

	/**
	 * @param missingCategoryPenalty
	 *            the missingCategoryPenalty to set
	 */
	@SuppressWarnings("static-access")
	@Value("${penalty.missingCategory}")
	public void setMissingCategoryPenalty(int missingCategoryPenalty) {
		this.missingCategoryPenalty = missingCategoryPenalty;
	}

	/**
	 * @param leftoverCategoryPenalty
	 *            the leftoverCategoryPenalty to set
	 */
	@SuppressWarnings("static-access")
	@Value("${penalty.leftoverCategory}")
	public void setLeftoverCategoryPenalty(int leftoverCategoryPenalty) {
		this.leftoverCategoryPenalty = leftoverCategoryPenalty;
	}

}
