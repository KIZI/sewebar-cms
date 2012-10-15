package xquerysearch.domain.arbquery.querysettings;

/**
 * Enum representing Results Analysis types stated in QuerySettings query element.
 * 
 * @author Tomas Marek
 *
 */
public enum QueryResultsAnalysis {

	NONE("None"), FUZZY("Fuzzy"), CLUSTERING("Clustering"), GROUPING("Grouping");
	
	private String text;
	
	/**
	 * 
	 */
	private QueryResultsAnalysis(String text) {
		this.text = text;
	}
	
	/**
	 * @return the text
	 */
	public String getText() {
		return text;
	}
}
