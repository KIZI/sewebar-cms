package xquerysearch.domain.arbquery;

import java.util.List;

/**
 * Domain object representing Coefficient element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class Coefficient {

	private String type;
	private List<String> categories;

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
	 * @return the categories
	 */
	public List<String> getCategories() {
		return categories;
	}

	/**
	 * @param categories
	 *            the categories to set
	 */
	public void setCategories(List<String> categories) {
		this.categories = categories;
	}

}
