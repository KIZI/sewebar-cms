package xquerysearch.analysis;

import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.analysis.ResultAnalysisOutput;
import xquerysearch.domain.result.Rule;

/**
 * Analyzer for {@link AssociationRuleInternal}.
 * 
 * @author Tomas Marek
 * 
 */
public class AssociationRuleAnalyzer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private AssociationRuleAnalyzer() {
	}

	/**
	 * Analyzes {@link Rule} represented by
	 * {@link AssociationRuleInternal}.
	 * 
	 * @param ari
	 * @return {@link ResultAnalysisOutput} describing {@link Rule}
	 */
	public static ResultAnalysisOutput analyze(AssociationRuleInternal ari) {
		ResultAnalysisOutput output = new ResultAnalysisOutput();

		output.setAntecedentBbaCount(ari.getAntecedentBbas().size());
		output.setConsequentBbaCount(ari.getConsequentBbas().size());
		output.setConditionBbaCount(ari.getConditionBbas().size());

		return output;
	}

}
