package xquerysearch.domain;

/**
 * Domain object representing result of query.
 * 
 * @author Tomas Marek
 *
 */
public class Result {
	
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

}