package izi_repository.clustering.computing;

import izi_repository.domain.Centroid;
import izi_repository.domain.arbquery.querysettings.ClusteringDistanceFormulaType;
import izi_repository.domain.result.Result;

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

			double[] centroidFinalVector = new double[getAllItemsCount(centroidVector)];
			double[] resultFinalVector = new double[getAllItemsCount(resultCompliance)];

			int position = 0;
			for (int i = 0; i < centroidVector.length; i++) {
				for (int j = 0; j < centroidVector[i].length; j++) {
					centroidFinalVector[position] = centroidVector[i][j];
					resultFinalVector[position] = resultCompliance[i][j];
					position++;
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

	/**
	 * Returns count of all items in 2-dimensional vector
	 * 
	 * @param vector
	 * @return
	 */
	private static int getAllItemsCount(double[][] vector) {
		int count = 0;
		for (int i = 0; i < vector.length; i++) {
			for (int j = 0; j < vector[i].length; j++) {
				count++;
			}
		}

		return count;
	}
}
