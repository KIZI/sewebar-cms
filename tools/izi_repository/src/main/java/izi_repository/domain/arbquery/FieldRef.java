package izi_repository.domain.arbquery;

/**
 * Domain object representing FieldRef element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class FieldRef {

	private String dictionary;
	private String value;

	/**
	 * @return the dictionary
	 */
	public String getDictionary() {
		return dictionary;
	}

	/**
	 * @param dictionary
	 *            the dictionary to set
	 */
	public void setDictionary(String dictionary) {
		this.dictionary = dictionary;
	}

	/**
	 * @return the value
	 */
	public String getValue() {
		return value;
	}

	/**
	 * @param value
	 *            the value to set
	 */
	public void setValue(String value) {
		this.value = value;
	}

}
