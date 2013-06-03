package izi_repository.domain;

/**
 * Domain object representing query.
 * 
 * @author Tomas Marek
 *
 */
public class Query {
	
	private String queryBody;
	
	/**
	 * Constructor for query object.
	 */
	public Query(String queryBody) {
		this.queryBody = queryBody;
	}

	/**
	 * @return the queryBody
	 */
	public String getQueryBody() {
		return queryBody;
	}

	/**
	 * @param queryBody the queryBody to set
	 */
	public void setQueryBody(String queryBody) {
		this.queryBody = queryBody;
	}

}
