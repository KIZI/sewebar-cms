package xquerysearch.clustering.computing;

import xquerysearch.domain.Centroid;
import xquerysearch.domain.arbquery.querysettings.ClusteringDistanceFormulaType;
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
	 * Compares {@link Centroid} and {@link Result} and returns similarity
	 * coefficient. Formula for computing distance between object can be
	 * specified, otherwise default one is used.
	 * 
	 * @param centroid
	 * @param result
	 * @return
	 */
	public static double compare(Centroid centroid, Result result, String formulaType) {
		if (centroid != null && result != null) {
			ClusteringDistanceFormulaType formulaTypeMapped = ClusteringDistanceFormulaType
					.convert(formulaType);

			double[][] centroidVector = centroid.getVector();
			double[][] resultCompliance = result.getQueryCompliance();

			double[] centroidFinalVector = new double[centroidVector.length];
			double[] resultFinalVector = new double[resultCompliance.length];

			for (int i = 0; i < centroidVector.length; i++) {
				if (centroidVector[i].length == 1) {
					resultFinalVector[i] = resultCompliance[i][0];
					centroidFinalVector[i] = centroidVector[i][0];
				} else {
					double localSumCentroid = 0.0;
					double localSumResult = 0.0;
					for (int j = 0; i < centroidVector[i].length; j++) {
						localSumCentroid += centroidVector[i][j];
						localSumResult += resultCompliance[i][j];
					}
					centroidFinalVector[i] = (localSumCentroid / centroidVector[i].length);
					resultFinalVector[i] = (localSumResult / resultCompliance[i].length);
				}
			}

			switch (formulaTypeMapped) {
			case COSINE_COEFFICIENT:
				return analyzeWithCosineCoefficient(centroidFinalVector, resultFinalVector);
			case OVERLAP_COEFFICIENT:
				return analyzeWithOverlapCoefficient(centroidFinalVector, resultFinalVector);
			default:
				return analyzeWithCosineCoefficient(centroidFinalVector, resultFinalVector);
			}
		}
		return 0.0;
	}

	/**
	 * Computes distance between given vectors as overlap coefficient.
	 * 
	 * @param centroidVector
	 * @param resultVector
	 * @return
	 */
	private static double analyzeWithOverlapCoefficient(double[] centroidVector, double[] resultVector) {
		double sumOfMins = 0.0;
		double sumCentroid = 0.0;
		double sumResult = 0.0;
		for (int i = 0; i < centroidVector.length; i++) {
			double resultValue = resultVector[i];
			double centroidValue = centroidVector[i];

			sumResult += resultValue;
			sumCentroid += centroidValue;
			sumOfMins += Math.min(centroidValue, resultValue);
		}
		return sumOfMins / Math.min(sumCentroid, sumResult);
	}

	/**
	 * Computes distance between given vectors as cosine coefficient.
	 * 
	 * @param centroidVector
	 * @param resultVector
	 * @return
	 */
	private static double analyzeWithCosineCoefficient(double[] centroidVector, double[] resultVector) {
		double sumOfMultiplications = 0.0;
		double sumCentroidSq = 0.0;
		double sumResultSq = 0.0;
		for (int i = 0; i < centroidVector.length; i++) {
			double resultValue = resultVector[i];
			double centroidValue = centroidVector[i];

			sumOfMultiplications += (resultValue * centroidValue);
			sumCentroidSq += (centroidValue * centroidValue);
			sumResultSq += (resultValue * resultValue);
		}
		return (sumOfMultiplications / (Math.sqrt(sumResultSq) * Math.sqrt(sumCentroidSq)));
	}
}
