package xquerysearch.domain;

/**
 * Domain object representing result of query.
 * 
 * @author Tomas Marek
 *
 */
public class Result {
	
	private int foundAssocRulesCount;
	private int timeSpentInMs;
	private String resultBody;
	
	/**
	 * Constructor for result object.  
	 */
	public Result(String resultBody) {
		this.resultBody = resultBody;
	}

	/**
	 * @return the resultBody
	 */
	public String getResultBody() {
		return resultBody;
	}

	/**
	 * @param resultBody the resultBody to set
	 */
	public void setResultBody(String resultBody) {
		this.resultBody = resultBody;
	}

	/**
	 * @return the foundAssocRulesCount
	 */
	public int getFoundAssocRulesCount() {
		return foundAssocRulesCount;
	}

	/**
	 * @param foundAssocRulesCount the foundAssocRulesCount to set
	 */
	public void setFoundAssocRulesCount(int foundAssocRulesCount) {
		this.foundAssocRulesCount = foundAssocRulesCount;
	}

	/**
	 * @return the timeSpentInMs
	 */
	public int getTimeSpentInMs() {
		return timeSpentInMs;
	}

	/**
	 * @param timeSpentInMs the timeSpentInMs to set
	 */
	public void setTimeSpentInMs(int timeSpentInMs) {
		this.timeSpentInMs = timeSpentInMs;
	}

}
