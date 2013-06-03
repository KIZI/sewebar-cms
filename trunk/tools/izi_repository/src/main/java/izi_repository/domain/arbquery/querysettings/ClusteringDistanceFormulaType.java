package izi_repository.domain.arbquery.querysettings;

/**
 * Enum representing ClusterDistanceFormula types stated in QuerySettings query
 * element.
 * 
 * @author Tomas Marek
 * 
 */
public enum ClusteringDistanceFormulaType {
	COSINE_COEFFICIENT("Cosine"), OVERLAP_COEFFICIENT("Overlap");

	private String text;

	/**
	 * 
	 */
	private ClusteringDistanceFormulaType(String text) {
		this.text = text;
	}

	/**
	 * @return the text
	 */
	public String getText() {
		return text;
	}

	/**
	 * Converts given {@link String} value to {@link ClusteringDistanceFormulaType}.
	 * 
	 * @param value
	 * @return {@link GroupingType} if found, <tt>null</tt> otherwise
	 */
	public static ClusteringDistanceFormulaType convert(String value) {
		for (ClusteringDistanceFormulaType type : values()) {
			if (type.getText().equals(value)) {
				return type;
			}
		}
		return COSINE_COEFFICIENT;
	}
}
