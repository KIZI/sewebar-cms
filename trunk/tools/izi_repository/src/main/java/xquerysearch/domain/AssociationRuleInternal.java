package xquerysearch.domain;

import java.util.List;

import xquerysearch.domain.result.BBAForAnalysis;
import xquerysearch.domain.result.ImValue;
import xquerysearch.domain.result.datadescription.ResultDataDescription;

/**
 * Domain object representing Association Rule used for internal purposes.
 * 
 * @author Tomas Marek
 * 
 */
public class AssociationRuleInternal {

	private List<BBAForAnalysis> antecedentBbas;
	private List<BBAForAnalysis> consequentBbas;
	private List<BBAForAnalysis> conditionBbas;
	private List<ImValue> imValues;
	private Boolean interesting;
	private ResultDataDescription dataDescription;

	/**
	 * @return the antecedentBbas
	 */
	public List<BBAForAnalysis> getAntecedentBbas() {
		return antecedentBbas;
	}

	/**
	 * @param antecedentBbas
	 *            the antecedentBbas to set
	 */
	public void setAntecedentBbas(List<BBAForAnalysis> antecedentBbas) {
		this.antecedentBbas = antecedentBbas;
	}

	/**
	 * @return the consequentBbas
	 */
	public List<BBAForAnalysis> getConsequentBbas() {
		return consequentBbas;
	}

	/**
	 * @param consequentBbas
	 *            the consequentBbas to set
	 */
	public void setConsequentBbas(List<BBAForAnalysis> consequentBbas) {
		this.consequentBbas = consequentBbas;
	}

	/**
	 * @return the conditionBbas
	 */
	public List<BBAForAnalysis> getConditionBbas() {
		return conditionBbas;
	}

	/**
	 * @param conditionBbas
	 *            the conditionBbas to set
	 */
	public void setConditionBbas(List<BBAForAnalysis> conditionBbas) {
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

	/**
	 * @return the interesting
	 */
	public Boolean getInteresting() {
		return interesting;
	}

	/**
	 * @param interesting
	 *            the interesting to set
	 */
	public void setInteresting(Boolean interesting) {
		this.interesting = interesting;
	}

	/**
	 * @return the dataDescription
	 */
	public ResultDataDescription getDataDescription() {
		return dataDescription;
	}

	/**
	 * @param dataDescription
	 *            the dataDescription to set
	 */
	public void setDataDescription(ResultDataDescription dataDescription) {
		this.dataDescription = dataDescription;
	}

}
