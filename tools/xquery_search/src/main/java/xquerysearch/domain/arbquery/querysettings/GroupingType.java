package xquerysearch.domain.arbquery.querysettings;

/**
 * Enum representing GroupBy types stated in QuerySettings query element.
 * 
 * @author Tomas Marek
 *
 */
public enum GroupingType {

	FIELDREF("FieldRef"), FIELDREF_BY_CEDENT("FieldRefByCedent"), CATEGORY("Category"), RULE_LENGTH("RuleLength"), RULE_LENGTH_BY_CEDENT("RuleLengthByCedent");
	
	private String text;
	
	/**
	 * 
	 */
	private GroupingType(String text) {
		this.text = text;
	}
	
	/**
	 * @return the text
	 */
	public String getText() {
		return text;
	}
}
