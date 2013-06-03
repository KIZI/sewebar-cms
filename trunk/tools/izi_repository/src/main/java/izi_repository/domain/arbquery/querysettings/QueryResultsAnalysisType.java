package izi_repository.domain.arbquery.querysettings;

/**
 * Enum representing Results Analysis types stated in QuerySettings query element.
 * 
 * @author Tomas Marek
 *
 */
public enum QueryResultsAnalysisType {

	NONE("None"), FUZZY("Fuzzy"), CLUSTERING("Clustering"), GROUPING("Grouping");
	
	private String text;
	
	/**
	 * 
	 */
	private QueryResultsAnalysisType(String text) {
		this.text = text;
	}
	
	/**
	 * @return the text
	 */
	public String getText() {
		return text;
	}
	
	/**
	 * Converts given {@link String} value to {@link QueryResultsAnalysisType}.
	 * 
	 * @param value
	 * @return {@link QueryResultsAnalysisType} if found, <tt>null</tt> otherwise
	 */
	public static QueryResultsAnalysisType convert(String value) {
		for (QueryResultsAnalysisType type : values()) {
			if (type.getText().equals(value)) {
				return type;
			}
		}
		return NONE;
	}
}
