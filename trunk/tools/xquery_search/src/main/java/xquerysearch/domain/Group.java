package xquerysearch.domain;

import java.util.List;

import xquerysearch.domain.result.Result;

/**
 * Domain object representing group.
 * 
 * @author Tomas Marek
 * 
 */
public class Group {

	private long id;
	private List<Result> results;

	/**
	 * @return the id
	 */
	public long getId() {
		return id;
	}

	/**
	 * @param id
	 *            the id to set
	 */
	public void setId(long id) {
		this.id = id;
	}

	/**
	 * @return the results
	 */
	public List<Result> getResults() {
		return results;
	}

	/**
	 * @param results
	 *            the results to set
	 */
	public void setResults(List<Result> results) {
		this.results = results;
	}

}
