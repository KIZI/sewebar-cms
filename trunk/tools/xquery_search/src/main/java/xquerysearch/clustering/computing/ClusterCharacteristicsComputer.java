package xquerysearch.clustering.computing;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.Centroid;
import xquerysearch.domain.Cluster;
import xquerysearch.domain.result.Result;

/**
 * Class for computing characteristics of {@link Cluster}.
 * 
 * @author Tomas Marek
 * 
 */
public class ClusterCharacteristicsComputer {

	/**
	 * Default constructor - made private, class provides only static methods
	 */
	private ClusterCharacteristicsComputer() {
	}

	/**
	 * Updates cluster by recomputing its centroid.
	 * 
	 * @param cluster
	 */
	public static void updateCluster(Cluster cluster) {
		if (cluster != null) {
			updateCentroid(cluster.getCentroid(), cluster.getResults());
		}
	}

	/**
	 * Recomputes centroid's vector.
	 * 
	 * @param centroid
	 * @param results
	 */
	private static void updateCentroid(Centroid centroid, List<Result> results) {
		if (centroid != null && results != null) {
			List<double[][]> vectors = new ArrayList<double[][]>();
			for (Result result : results) {
				vectors.add(result.getQueryCompliance());
			}
			double[][] averageVector = getAverageVector(vectors);
			centroid.setVector(averageVector);
		}
	}

	/**
	 * Returns vector with average values from given vectors.
	 * 
	 * @param vectors
	 * @return
	 */
	private static double[][] getAverageVector(List<double[][]> vectors) {
		double[][] avgVector = null;
		for (int i = 0; i < vectors.size(); i++) {
			if (i == 0) {
				avgVector = vectors.get(i);
			} else {
				avgVector = combineVectors(avgVector, vectors.get(i));
			}
		}
		return avgVector;
	}

	/**
	 * Combines 2 given vectors into single vector using average values.
	 * 
	 * @param vector1
	 * @param vector2
	 * @return
	 */
	private static double[][] combineVectors(double[][] vector1, double[][] vector2) {
		double[][] ret = new double[vector1.length][];
		for (int i = 0; i < vector1.length; i++) {
			double[] innerVector = new double[vector1[i].length];
			for (int j = 0; j < vector1[i].length; j++) {
				double value1 = vector1[i][j];
				double value2 = vector2[i][j];

				innerVector[j] = (value1 + value2) / 2;
			}
			ret[i] = innerVector;
		}
		return ret;
	}
}
