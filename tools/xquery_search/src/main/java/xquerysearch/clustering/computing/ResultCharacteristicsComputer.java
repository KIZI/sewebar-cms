package xquerysearch.clustering.computing;

import xquerysearch.domain.Centroid;
import xquerysearch.domain.result.Result;

/**
 * Class for computing characteristics of {@link Result}.
 * 
 * @author Tomas Marek
 * 
 */
public class ResultCharacteristicsComputer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ResultCharacteristicsComputer() {
	}

	/**
	 * TODO documentation
	 * 
	 * @param result
	 */
	public static void compute(Result result) {

	}

	/**
	 * Compares {@link Centroid} and {@link Result} and returns similarity
	 * coefficient.
	 * 
	 * @param centroid
	 * @param result
	 * @return
	 */
	public static double compare(Centroid centroid, Result result) {

		return 0.0;
	}
}
