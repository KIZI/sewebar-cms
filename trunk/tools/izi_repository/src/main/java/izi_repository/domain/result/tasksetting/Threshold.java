package izi_repository.domain.result.tasksetting;

/**
 * Domain object representing Threshold element from TaskSetting from query
 * result.
 * 
 * @author Tomas Marek
 * 
 */
public class Threshold {

	private String type;
	private Double value;

	/**
	 * @return the type
	 */
	public String getType() {
		return type;
	}

	/**
	 * @param type
	 *            the type to set
	 */
	public void setType(String type) {
		this.type = type;
	}

	/**
	 * @return the value
	 */
	public Double getValue() {
		return value;
	}

	/**
	 * @param value
	 *            the value to set
	 */
	public void setValue(Double value) {
		this.value = value;
	}

	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		return "<Threshold type=\"" + type + "\">" + value + "</Threshold>";
	}
}
