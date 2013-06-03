package izi_repository.domain.arbquery;

/**
 * Domain object representing BBASetting element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class BbaSetting {

	private String id;
	private String text;
	private FieldRef fieldRef;
	private Coefficient coefficient;

	/**
	 * @return the id
	 */
	public String getId() {
		return id;
	}

	/**
	 * @param id
	 *            the id to set
	 */
	public void setId(String id) {
		this.id = id;
	}

	/**
	 * @return the fieldRef
	 */
	public FieldRef getFieldRef() {
		return fieldRef;
	}

	/**
	 * @param fieldRef
	 *            the fieldRef to set
	 */
	public void setFieldRef(FieldRef fieldRef) {
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
	 * @return the name
	 */
	public String getText() {
		return text;
	}

	/**
	 * @param text
	 *            the text to set
	 */
	public void setText(String text) {
		this.text = text;
	}

}
