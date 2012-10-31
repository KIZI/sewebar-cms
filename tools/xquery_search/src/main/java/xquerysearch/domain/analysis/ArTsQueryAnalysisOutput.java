package xquerysearch.domain.analysis;

/**
 * Domain object for storing results of ArTsQuery analysis.
 * 
 * @author Tomas Marek
 * 
 */
public class ArTsQueryAnalysisOutput {

	private int antecedentBbaCount;
	private int consequentBbaCount;
	private int conditionBbaCount;

	/**
	 * @return the antecedentBbaCount
	 */
	public int getAntecedentBbaCount() {
		return antecedentBbaCount;
	}

	/**
	 * @param antecedentBbaCount
	 *            the antecedentBbaCount to set
	 */
	public void setAntecedentBbaCount(int antecedentBbaCount) {
		this.antecedentBbaCount = antecedentBbaCount;
	}

	/**
	 * @return the consequentBbaCount
	 */
	public int getConsequentBbaCount() {
		return consequentBbaCount;
	}

	/**
	 * @param consequentBbaCount
	 *            the consequentBbaCount to set
	 */
	public void setConsequentBbaCount(int consequentBbaCount) {
		this.consequentBbaCount = consequentBbaCount;
	}

	/**
	 * @return the conditionBbaCount
	 */
	public int getConditionBbaCount() {
		return conditionBbaCount;
	}

	/**
	 * @param conditionBbaCount
	 *            the conditionBbaCount to set
	 */
	public void setConditionBbaCount(int conditionBbaCount) {
		this.conditionBbaCount = conditionBbaCount;
	}

}
