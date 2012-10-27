package xquerysearch.domain.result;

import java.util.ArrayList;
import java.util.List;

/**
 * Domain object representing set of {@link Result}s.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultSet {

	private List<Result> results = new ArrayList<Result>();

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
