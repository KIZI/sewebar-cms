package xquerysearch.fuzzysearch.evaluator;

import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import org.springframework.beans.factory.annotation.Value;

import xquerysearch.analysis.ArQueryAnalyzer;
import xquerysearch.analysis.ArTsQueryAnalyzer;
import xquerysearch.analysis.AssociationRuleAnalyzer;
import xquerysearch.analysis.TaskSettingAnalyzer;
import xquerysearch.domain.ArQueryInternal;
import xquerysearch.domain.ArTsQueryInternal;
import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.TaskSettingInternal;
import xquerysearch.domain.analysis.ArQueryAnalysisOutput;
import xquerysearch.domain.analysis.ArTsQueryAnalysisOutput;
import xquerysearch.domain.analysis.ResultAnalysisOutput;
import xquerysearch.domain.analysis.TaskSettingAnalysisOutput;
import xquerysearch.domain.arbquery.BbaSetting;
import xquerysearch.domain.result.BBAForAnalysis;

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

	private static int notInterestingPenalty;

	private static int bbaConcretenessPenalty;

	private static int disjunctionPenalty;

	/**
	 * {@inheritDoc}
	 */
	@Override
	public double[][] evaluate(AssociationRuleInternal ari, ArQueryInternal aqi) {
		Double resultCompliance = DEFAULT_COMPLIANCE;

		ResultAnalysisOutput resultAnalysis = AssociationRuleAnalyzer.analyze(ari);
		// TODO move one level up? - performance
		ArQueryAnalysisOutput queryAnalysis = ArQueryAnalyzer.analyze(aqi);

		resultCompliance -= checkBbaCounts(resultAnalysis, queryAnalysis);
		resultCompliance -= checkInterestingness(ari);

		double[] antecedentBbaVector = evaluateBbas(ari.getAntecedentBbas(),
				aqi.getAntecedentBbaSettingList(), queryAnalysis.getConcretenessMap(),
				resultAnalysis.getConcretenessMap());
		double[] consequentBbaVector = evaluateBbas(ari.getConsequentBbas(),
				aqi.getConsequentBbaSettingList(), queryAnalysis.getConcretenessMap(),
				resultAnalysis.getConcretenessMap());
		double[] conditionBbaVector = evaluateBbas(ari.getConditionBbas(), aqi.getConditionBbaSettingList(),
				queryAnalysis.getConcretenessMap(), resultAnalysis.getConcretenessMap());

		return new double[][] { antecedentBbaVector, consequentBbaVector, conditionBbaVector,
				new double[] { resultCompliance } };
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public double[][] evaluate(TaskSettingInternal tsi, ArTsQueryInternal atqi) {
		Double compliance = DEFAULT_COMPLIANCE;

		TaskSettingAnalysisOutput tsAnalysis = TaskSettingAnalyzer.analyze(tsi);
		ArTsQueryAnalysisOutput queryAnalysis = ArTsQueryAnalyzer.analyze(atqi);

		compliance -= checkBbaCounts(tsAnalysis, queryAnalysis);

		return new double[][] { new double[] { compliance } };
	}

	/**
	 * Evaluates counts of BBAs.
	 * 
	 * @return
	 */
	private double checkBbaCounts(ResultAnalysisOutput resultAnalysis, ArQueryAnalysisOutput queryAnalysis) {

		double antecedentPenalty = Math.abs(resultAnalysis.getAntecedentBbaCount()
				- queryAnalysis.getAntecedentBbaCount())
				* bbaCountPenalty;
		double consequentPenalty = Math.abs(resultAnalysis.getConsequentBbaCount()
				- queryAnalysis.getConsequentBbaCount())
				* bbaCountPenalty;
		double conditionPenalty = Math.abs(resultAnalysis.getConditionBbaCount()
				- queryAnalysis.getConditionBbaCount())
				* bbaCountPenalty;

		return antecedentPenalty + consequentPenalty + conditionPenalty;
	}

	private double checkBbaCounts(TaskSettingAnalysisOutput tsao, ArTsQueryAnalysisOutput atqao) {
		double antecedentPenalty = Math.abs(tsao.getAntecedentBbaCount() - atqao.getAntecedentBbaCount())
				* bbaCountPenalty;
		double consequentPenalty = Math.abs(tsao.getConsequentBbaCount() - atqao.getConsequentBbaCount())
				* bbaCountPenalty;
		double conditionPenalty = Math.abs(tsao.getConditionBbaCount() - atqao.getConditionBbaCount())
				* bbaCountPenalty;

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
	private double[] evaluateBbas(List<BBAForAnalysis> bbas, List<BbaSetting> bbaSettings, Map<String, Double> concretenessMapQuery, Map<String, Double> concretenessMapResult) {
		double[] ret = new double[bbaSettings.size()];

		for (int i = 0; i < bbaSettings.size(); i++) {
			BbaSetting bbaSetting = bbaSettings.get(i);
			BBAForAnalysis bba = getCorrespondingBba(bbaSetting.getFieldRef().getValue(), bbas);
			double compliance = DEFAULT_COMPLIANCE;
			compliance -= evaluateBba(bba, bbaSetting);
			if (bba != null && bba.getTransformationDictionary() != null
					&& bba.getTransformationDictionary().getFieldName() != null) {
				compliance -= checkConcreteness(concretenessMapQuery, concretenessMapResult, bba
						.getTransformationDictionary().getFieldName());
			}
			ret[i] = compliance;
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
	private double evaluateBba(BBAForAnalysis bba, BbaSetting bbaSetting) {
		double ret = 0;
		if (bba != null && bbaSetting != null) {
			ret += evaluatesCategories(bba.getTransformationDictionary().getCatNames(), bbaSetting
					.getCoefficient().getCategories());
			if (bba.isDisjunctive() == true) {
				ret += disjunctionPenalty;
			}
		}
		return ret;
	}

	/**
	 * Retrieves matching BBA from list of found AR's BBAs to ArQuery
	 * BbaSetting. Field Name (e.g. District) is used to find match.
	 * 
	 * @param fieldRef
	 * @param bbas
	 * @return
	 */
	private BBAForAnalysis getCorrespondingBba(String fieldRef, List<BBAForAnalysis> bbas) {
		if (fieldRef != null && bbas != null) {
			for (BBAForAnalysis bba : bbas) {
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
	private double evaluatesCategories(List<String> bbaCategories, List<String> bbaSettingCategories) {
		double compliance = 0;
		if (bbaCategories != null && bbaSettingCategories != null) {
			for (String bbaSetCategory : bbaSettingCategories) {
				if (hasCorrespondingCategory(bbaSetCategory, bbaCategories) == false) {
					compliance += missingCategoryPenalty;
				}
				int leftovers = getLeftoverCategoriesCount(bbaSettingCategories, bbaCategories);
				compliance += (leftovers * leftoverCategoryPenalty);
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
	private boolean hasCorrespondingCategory(String category, List<String> bbaCategories) {
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
	private int getLeftoverCategoriesCount(List<String> bbaSetCategories, List<String> bbaCategories) {
		Set<String> bbaCategoriesTemp = new HashSet<String>(bbaCategories);
		for (String bbaSetCategory : bbaSetCategories) {
			bbaCategoriesTemp.remove(bbaSetCategory);
		}
		return bbaCategoriesTemp.size();
	}

	/**
	 * Returns penalty for non-interesting association rule if the rule is not
	 * interesting. Value is (and should be) positive.
	 * 
	 * @param ari
	 * @return
	 */
	private double checkInterestingness(AssociationRuleInternal ari) {
		if (ari.getInteresting() != null && ari.getInteresting() == false) {
			return notInterestingPenalty;
		}
		return 0;
	}

	/**
	 * TODO documentation
	 * 
	 * @param concretenessMapQuery
	 * @param concretenessMapResult
	 * @param fieldRef
	 * @return
	 */
	private double checkConcreteness(Map<String, Double> concretenessMapQuery, Map<String, Double> concretenessMapResult, String fieldRef) {
		Double resultValue = concretenessMapResult.get(fieldRef);
		Double queryValue = concretenessMapQuery.get(fieldRef);
		if (queryValue > 0 && resultValue > 0) {
			if ((resultValue / queryValue) > 1) {
				return ((resultValue / queryValue) * bbaConcretenessPenalty);
			}
		}
		return 0;
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
	@Value("${penalty.missingCategory}")
	public void setMissingCategoryPenalty(int missingCategoryPenalty) {
		FuzzySearchEvaluatorImpl.missingCategoryPenalty = missingCategoryPenalty;
	}

	/**
	 * @param leftoverCategoryPenalty
	 *            the leftoverCategoryPenalty to set
	 */
	@Value("${penalty.leftoverCategory}")
	public void setLeftoverCategoryPenalty(int leftoverCategoryPenalty) {
		FuzzySearchEvaluatorImpl.leftoverCategoryPenalty = leftoverCategoryPenalty;
	}

	/**
	 * @param notInterestingPenalty
	 *            the notInterestingPenalty to set
	 */
	@Value("${penalty.notInteresting}")
	public void setNotInterestingPenalty(int notInterestingPenalty) {
		FuzzySearchEvaluatorImpl.notInterestingPenalty = notInterestingPenalty;
	}

	/**
	 * @param bbaConcretenessPenalty
	 *            the bbaConcretenessPenalty to set
	 */
	@Value("${penalty.concreteness}")
	public void setBbaConcretenessPenalty(int bbaConcretenessPenalty) {
		FuzzySearchEvaluatorImpl.bbaConcretenessPenalty = bbaConcretenessPenalty;
	}

	/**
	 * @param disjunctionPenalty
	 *            the disjunctionPenalty to set
	 */
	@Value("${penalty.disjunction}")
	public void setDisjunctionPenalty(int disjunctionPenalty) {
		FuzzySearchEvaluatorImpl.disjunctionPenalty = disjunctionPenalty;
	}
}
