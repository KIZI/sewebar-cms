package izi_repository.domain.result;

/**
 * Domain object representing IMValue element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class ImValue {

	private String name;
	private String value;

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

	/**
	 * @{inheritDoc
	 */
	@Override
	public String toString() {
		return "<IMValue name=\"" + name + "\">" + value + "</IMValue>";
	}
}
