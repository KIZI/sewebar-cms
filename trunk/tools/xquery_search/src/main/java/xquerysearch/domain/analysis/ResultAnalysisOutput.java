package xquerysearch.domain.analysis;

import java.util.HashMap;
import java.util.Map;

/**
 * Domain object for storing result of result analysis.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultAnalysisOutput {

	private int antecedentBbaCount;
	private int consequentBbaCount;
	private int conditionBbaCount;
	private Map<String, Double> concretenessMap = new HashMap<String, Double>();

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

	/**
	 * @return the concretenessMap
	 */
	public Map<String, Double> getConcretenessMap() {
		return concretenessMap;
	}
}
