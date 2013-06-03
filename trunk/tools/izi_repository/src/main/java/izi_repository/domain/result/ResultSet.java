package izi_repository.domain.result;

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

}
