package izi_repository.domain.arbquery.querysettings;

/**
 * Enum representing targets of query stated in QuerySettings element.
 * 
 * @author Tomas Marek
 *
 */
public enum QueryTargetType {
	
	ASSOCIATION_RULE("AssociationRule"), TASK_SETTING("TaskSetting");
	
	private String text;
	
	/**
	 * 
	 */
	private QueryTargetType(String text) {
		this.text = text;
	}
	
	/**
	 * @return the text
	 */
	public String getText() {
		return text;
	}

}
