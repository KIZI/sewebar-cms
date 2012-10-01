package xquerysearch.sorting;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Set;

import xquerysearch.domain.result.Result;

/**
 * Class providing data sorting for output.
 * 
 * @author Tomas Marek
 * 
 */
public class OutputFuzzySorter {

	/**
	 * Given {@link Set} of {@link Result}s is converted into {@link ArrayList}
	 * and sorted using {@link FuzzyComparator}.
	 * Results are ordered from one with higher query compliance to one with the lowest.
	 * 
	 * @param results
	 *            {@link Set} of {@link Result}s to sort
	 * @return sorted {@link ArrayList} in descending order
	 */
	public static List<Result> sortByCompliance(Set<Result> results) {
		List<Result> ret = new ArrayList<Result>(results);
		Collections.sort(ret, new FuzzyComparator());
		Collections.reverse(ret);
		return ret;
	}

}
