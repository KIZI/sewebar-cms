package xquerysearch.domain.result;

import java.util.Set;

/**
 * Domain object representing set of {@link Result}s.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultSet {

	private Set<Result> results;

	/**
	 * @return the results
	 */
	public Set<Result> getResults() {
		return results;
	}

	/**
	 * @param results
	 *            the results to set
	 */
	public void setResults(Set<Result> results) {
		this.results = results;
	}

}
