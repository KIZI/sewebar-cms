package izi_repository.transformation;

import izi_repository.domain.AssociationRuleInternal;
import izi_repository.domain.result.BBAForAnalysis;
import izi_repository.domain.result.Cedent;
import izi_repository.domain.result.ImValue;
import izi_repository.domain.result.Rule;
import izi_repository.domain.result.datadescription.ResultDataDescription;
import izi_repository.utils.ResultUtils;

import java.util.ArrayList;


/**
 * Transformer for transformation from {@link Rule} to
 * {@link AssociationRuleInternal}.
 * 
 * @author Tomas Marek
 * 
 */
public class AssociationRuleToInternalTransformer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private AssociationRuleToInternalTransformer() {
	}

	/**
	 * Transforms {@link Rule} to {@link AssociationRuleInternal}.
	 * 
	 * @param rule
	 * @return
	 */
	public static AssociationRuleInternal transform(Rule rule, ResultDataDescription dataDescription) {
		AssociationRuleInternal ari = new AssociationRuleInternal();

		ari.setDataDescription(dataDescription);
		
		Cedent antecedent = rule.getAntecedent();
		Cedent consequent = rule.getConsequent();
		Cedent condition = rule.getCondition();

		ari.setAntecedentBbas(new ArrayList<BBAForAnalysis>(ResultUtils.getBbasForAnalysisFromCedent(antecedent, false)));
		ari.setConsequentBbas(new ArrayList<BBAForAnalysis>(ResultUtils.getBbasForAnalysisFromCedent(consequent, false)));
		ari.setConditionBbas(new ArrayList<BBAForAnalysis>(ResultUtils.getBbasForAnalysisFromCedent(condition, false)));

		ari.setImValues(new ArrayList<ImValue>(rule.getImValues()));

		if (rule.getAnnotation() != null) {
			String interestingness = rule.getAnnotation().getInterestingness();
			if (interestingness.equals("interesting")) {
				ari.setInteresting(true);
			} else if (interestingness.equals("not interesting")) {
				ari.setInteresting(false);
			} else {
				ari.setImValues(null);
			}
		}
		
		return ari;
	}

}
