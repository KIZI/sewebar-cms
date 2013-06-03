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
	 * @{inheritDoc}
	 */
	@Override
	public int compare(Result r1, Result r2) {
		double r1Average = getAverageForArray(r1.getQueryCompliance());
		double r2Average = getAverageForArray(r2.getQueryCompliance());
		
		if (r1Average > r2Average) {
			return 1;
		} else if (r1Average < r2Average) {
			return -1;
		} else {
			return 0;
		}
	}

	/**
	 * Computes average value from given 2-dimensional array of Doubles.
	 * 
	 * @param array
	 * @return
	 */
	private double getAverageForArray(double[][] array) {
		if (array != null) {
			double sum = 0.0;
			for (int i = 0; i < array.length; i++) {
				sum += getAverageForInnerArray(array[i]);
			}
			return sum/array.length;
		}
		return 0.0;
	}
	
	/**
	 * Computes average value from given 1-dimensional array of Doubles.
	 * 
	 * @param innerArray
	 * @return
	 */
	private double getAverageForInnerArray(double[] innerArray) {
		int arrayLength = innerArray.length;
		if (innerArray != null && arrayLength > 0) {
			double sum = 0.0;
			for (int i = 0; i < arrayLength; i++) {
				sum += innerArray[i];
			}
			return sum/arrayLength;
		}
		return 0.0;
	}
}
