package xquerysearch.analysis;

import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.analysis.ResultAnalysisOutput;
import xquerysearch.domain.result.Result;

/**
 * Analyzes {@link AssociationRuleInternal}.
 * 
 * @author Tomas Marek
 * 
 */
public class AssocitaionRuleAnalyzer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private AssocitaionRuleAnalyzer() {
	}

	/**
	 * Analyzes {@link AssociationRuleInternal} represented by
	 * {@link AssociationRuleInternal}.
	 * 
	 * @param ari
	 * @return {@link ResultAnalysisOutput} describing {@link Result}
	 */
	public static ResultAnalysisOutput analyze(AssociationRuleInternal ari) {
		ResultAnalysisOutput output = new ResultAnalysisOutput();

		output.setAntecedentBbaCount(ari.getAntecedentBbas().size());
		output.setConsequentBbaCount(ari.getConsequentBbas().size());
		output.setConditionBbaCount(ari.getConditionBbas().size());

		return output;
	}

}
