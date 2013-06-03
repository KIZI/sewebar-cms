package izi_repository.sorting;

import izi_repository.domain.result.Result;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Set;


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
	 *            {@link List} of {@link Result}s to sort
	 * @return sorted {@link ArrayList} in descending order
	 */
	public static List<Result> sortByCompliance(List<Result> results) {
		Collections.sort(results, new FuzzyComparator());
		Collections.reverse(results);
		return results;
	}

}
