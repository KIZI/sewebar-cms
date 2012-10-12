package xquerysearch.transformation;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.Set;

import xquerysearch.domain.AssociationRuleInternal;
import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.Cedent;
import xquerysearch.domain.result.DBA;
import xquerysearch.domain.result.ImValue;
import xquerysearch.domain.result.Rule;

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
	public static AssociationRuleInternal transform(Rule rule) {
		AssociationRuleInternal ari = new AssociationRuleInternal();

		Cedent antecedent = rule.getAntecedent();
		Cedent consequent = rule.getConsequent();
		Cedent condition = rule.getCondition();

		ari.setAntecedentBbas(new ArrayList<BBA>(getBbasFromCedent(antecedent)));
		ari.setConsequentBbas(new ArrayList<BBA>(getBbasFromCedent(consequent)));
		ari.setConditionBbas(new ArrayList<BBA>(getBbasFromCedent(condition)));

		ari.setImValues(new ArrayList<ImValue>(rule.getImValues()));

		return ari;
	}

	/**
	 * Goes through cedent's {@link DBA}s and retrieves their {@link BBA}s.
	 * 
	 * @param cedent
	 * @return
	 */
	private static Set<BBA> getBbasFromCedent(Cedent cedent) {
		Set<BBA> ret = new HashSet<BBA>();
		if (cedent != null) {
			Set<DBA> dbas = cedent.getDbas();
			if (dbas != null) {
				for (DBA dba : dbas) {
					ret.addAll(getBbasFromDba(dba));
				}
			}
		}
		return ret;
	}

	/**
	 * Goes through {@link DBA}s and retrieves their {@link BBA}s.
	 * 
	 * @param dba
	 * @return
	 */
	private static Set<BBA> getBbasFromDba(DBA dba) {
		Set<BBA> ret = new HashSet<BBA>();
		if (dba != null) {
			Set<BBA> bbas = dba.getBbas();
			Set<DBA> dbas = dba.getDbas();

			if (bbas != null) {
				for (BBA bba : bbas) {
					ret.add(bba);
				}
			}

			if (dbas != null) {
				for (DBA dbaOfDba : dbas) {
					ret.addAll(getBbasFromDba(dbaOfDba));
				}
			}
		}
		return ret;
	}
}
