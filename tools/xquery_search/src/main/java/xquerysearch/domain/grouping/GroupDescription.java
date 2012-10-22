package xquerysearch.domain.grouping;

import java.util.ArrayList;
import java.util.List;

/**
 * Domain object representing description of {@link Group}.
 * 
 * @author Tomas Marek
 * 
 */
public class GroupDescription {

	private String fieldRefName;
	private List<String> categories = new ArrayList<String>();

	/**
	 * @return the categories
	 */
	public List<String> getCategories() {
		return categories;
	}

	/**
	 * @return the fieldRefName
	 */
	public String getFieldRefName() {
		return fieldRefName;
	}

	/**
	 * @param fieldRefName
	 *            the fieldRefName to set
	 */
	public void setFieldRefName(String fieldRefName) {
		this.fieldRefName = fieldRefName;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("[");
		String connective = "";
		if (fieldRefName != null) {
			ret.append(connective);
			ret.append("FieldRef: " + fieldRefName);
			connective = ", ";
		}
		if (categories != null) {
			ret.append(connective);
			ret.append("Categories: [");
			for (int i = 0; i < categories.size(); i++) {
				if (i > 0) {
					ret.append(", ");
				}
				ret.append(categories.get(i));
			}
			ret.append("]");
			connective = ", ";
		}
		ret.append("]");
		return ret.toString();
	}
}
