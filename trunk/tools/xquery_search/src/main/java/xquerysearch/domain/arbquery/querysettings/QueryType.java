package xquerysearch.domain.arbquery.querysettings;

/**
 * Enum representing types of query stated in QuerySettings element.
 * 
 * @author Tomas Marek
 * 
 */
public enum QueryType {
	
	NORMAL("Normal"), SHORTER("Shorter");
	
	private String text;
	
	/**
	 * 
	 */
	private QueryType(String text) {
		this.text = text;
	}
	
	/**
	 * @return the text
	 */
	public String getText() {
		return text;
	}
	
}
