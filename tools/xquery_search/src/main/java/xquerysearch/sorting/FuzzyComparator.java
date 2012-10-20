package xquerysearch.sorting;

import java.util.Comparator;

import xquerysearch.domain.result.Result;

/**
 * Comparator used in {@link OutputFuzzySorter}.
 * 
 * @author Tomas Marek
 * 
 */
public class FuzzyComparator implements Comparator<Result> {

	/**
	 * @{inheritDoc
	 */
	@Override
	public int compare(Result o1, Result o2) {
		Double[][] o1Compliance = o1.getQueryCompliance();
		Double[][] o2Compliance = o2.getQueryCompliance();

		// TODO need huge rework
		if (o1Compliance[0][0] > o2Compliance[0][0]) {
			return 1;
		} else if (o1Compliance[0][0] < o2Compliance[0][0]) {
			return -1;
		} else {
			return 0;
		}
	}

}
