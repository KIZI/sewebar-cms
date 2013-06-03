package izi_repository.domain.result.tasksetting;

/**
 * Domain object representing BBASetting element from TaskSetting from query
 * result.
 * 
 * @author Tomas Marek
 * 
 */
public class BBASetting {

	private String name;
	private String fieldRef;
	private Coefficient coefficient;

	/**
	 * @return the name
	 */
	public String getName() {
		return name;
	}

	/**
	 * @param name
	 *            the name to set
	 */
	public void setName(String name) {
		this.name = name;
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

	/**
	 * @return the coefficient
	 */
	public Coefficient getCoefficient() {
		return coefficient;
	}

	/**
	 * @param coefficient
	 *            the coefficient to set
	 */
	public void setCoefficient(Coefficient coefficient) {
		this.coefficient = coefficient;
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<BBASetting>");
		ret.append("<Name>" + name + "</Name>");
		ret.append("<FieldRef>" + fieldRef + "</FieldRef>");
		ret.append(coefficient.toString());
		ret.append("</BBASetting>");
		return ret.toString();
	}
}
