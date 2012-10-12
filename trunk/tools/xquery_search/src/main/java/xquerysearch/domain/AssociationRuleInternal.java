package xquerysearch.domain;

import java.util.List;

import xquerysearch.domain.result.BBA;
import xquerysearch.domain.result.ImValue;

/**
 * Domain object representing Association Rule used for internal purposes.
 * 
 * @author Tomas Marek
 * 
 */
public class AssociationRuleInternal {

	List<BBA> antecedentBbas;
	List<BBA> consequentBbas;
	List<BBA> conditionBbas;
	List<ImValue> imValues;

	/**
	 * @return the antecedentBbas
	 */
	public List<BBA> getAntecedentBbas() {
		return antecedentBbas;
	}

	/**
	 * @param antecedentBbas
	 *            the antecedentBbas to set
	 */
	public void setAntecedentBbas(List<BBA> antecedentBbas) {
		this.antecedentBbas = antecedentBbas;
	}

	/**
	 * @return the consequentBbas
	 */
	public List<BBA> getConsequentBbas() {
		return consequentBbas;
	}

	/**
	 * @param consequentBbas
	 *            the consequentBbas to set
	 */
	public void setConsequentBbas(List<BBA> consequentBbas) {
		this.consequentBbas = consequentBbas;
	}

	/**
	 * @return the conditionBbas
	 */
	public List<BBA> getConditionBbas() {
		return conditionBbas;
	}

	/**
	 * @param conditionBbas
	 *            the conditionBbas to set
	 */
	public void setConditionBbas(List<BBA> conditionBbas) {
		this.conditionBbas = conditionBbas;
	}

	/**
	 * @return the imValues
	 */
	public List<ImValue> getImValues() {
		return imValues;
	}

	/**
	 * @param imValues
	 *            the imValues to set
	 */
	public void setImValues(List<ImValue> imValues) {
		this.imValues = imValues;
	}

}
