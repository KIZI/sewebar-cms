package xquerysearch.domain.arbquery;

/**
 * Domain object representing Params element from Query settings.
 * 
 * @author Tomas Marek
 * 
 */
public class Params {
	private String groupBy;
	private String fieldRef;

	/**
	 * @return the groupBy
	 */
	public String getGroupBy() {
		return groupBy;
	}

	/**
	 * @param groupBy
	 *            the groupBy to set
	 */
	public void setGroupBy(String groupBy) {
		this.groupBy = groupBy;
	}

	/**
	 * @return the fieldRef
	 */
	public String getFieldRef() {
		return fieldRef;
	}

	/**
	 * @param fieldRef
	 *            the fieldRef to set
	 */
	public void setFieldRef(String fieldRef) {
		this.fieldRef = fieldRef;
	}

}
