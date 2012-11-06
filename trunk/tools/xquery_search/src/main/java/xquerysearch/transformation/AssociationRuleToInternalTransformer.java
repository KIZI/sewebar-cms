package xquerysearch.transformation;

import java.util.ArrayList;

import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.Cedent;
import xquerysearch.domain.result.ImValue;
import xquerysearch.domain.result.Rule;
import xquerysearch.domain.result.datadescription.ResultDataDescription;
import xquerysearch.utils.ResultUtils;

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

		ari.setAntecedentBbas(new ArrayList<BBA>(ResultUtils.getBbasFromCedent(antecedent)));
		ari.setConsequentBbas(new ArrayList<BBA>(ResultUtils.getBbasFromCedent(consequent)));
		ari.setConditionBbas(new ArrayList<BBA>(ResultUtils.getBbasFromCedent(condition)));

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
