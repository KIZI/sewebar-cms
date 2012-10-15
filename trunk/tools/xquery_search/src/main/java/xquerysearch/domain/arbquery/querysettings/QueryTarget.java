package xquerysearch.domain.arbquery.querysettings;

/**
 * Enum representing targets of query stated in QuerySettings element.
 * 
 * @author Tomas Marek
 *
 */
public enum QueryTarget {
	
	ASSOCIATION_RULE("AssociationRule"), TASK_SETTINGS("TaskSettings");
	
	private String text;
	
	/**
	 * 
	 */
	private QueryTarget(String text) {
		this.text = text;
	}
	
	/**
	 * @return the text
	 */
	public String getText() {
		return text;
	}

}
