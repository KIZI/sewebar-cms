package izi_repository.domain.arbquery.tasksetting;

/**
 * Domain object representing Coefficient from query to TaskSetting.
 * 
 * @author Tomas Marek
 * 
 */
public class Coefficient {

	private String type;
	private Integer minimalLength;
	private Integer maximalLength;
	private String category;

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
	 * @return the minimalLength
	 */
	public Integer getMinimalLength() {
		return minimalLength;
	}

	/**
	 * @param minimalLength
	 *            the minimalLength to set
	 */
	public void setMinimalLength(Integer minimalLength) {
		this.minimalLength = minimalLength;
	}

	/**
	 * @return the maximalLength
	 */
	public Integer getMaximalLength() {
		return maximalLength;
	}

	/**
	 * @param maximalLength
	 *            the maximalLength to set
	 */
	public void setMaximalLength(Integer maximalLength) {
		this.maximalLength = maximalLength;
	}

	/**
	 * @return the category
	 */
	public String getCategory() {
		return category;
	}

	/**
	 * @param category
	 *            the category to set
	 */
	public void setCategory(String category) {
		this.category = category;
	}

}
