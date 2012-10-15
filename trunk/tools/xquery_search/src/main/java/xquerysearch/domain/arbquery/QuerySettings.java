package xquerysearch.domain.arbquery;

/**
 * Domain object representing query settings from ArQuery.
 * 
 * @author Tomas Marek
 * 
 */
public class QuerySettings {

	private String type;
	private String target;
	private String resultsAnalysis;

	/**
	 * @return the type
	 */
	public String getType() {
		return type;
	}

	/**
	 * @param type
	 *            the type to set
	 */
	public void setType(String type) {
		this.type = type;
	}

	/**
	 * @return the target
	 */
	public String getTarget() {
		return target;
	}

	/**
	 * @param target
	 *            the target to set
	 */
	public void setTarget(String target) {
		this.target = target;
	}

	/**
	 * @return the resultsAnalysis
	 */
	public String getResultsAnalysis() {
		return resultsAnalysis;
	}

	/**
	 * @param resultsAnalysis
	 *            the resultsAnalysis to set
	 */
	public void setResultsAnalysis(String resultsAnalysis) {
		this.resultsAnalysis = resultsAnalysis;
	}

}
