package xquerysearch.domain.result;

import java.util.Set;

import org.apache.commons.lang.StringEscapeUtils;

/**
 * Domain object representing TransformationDictionary element from query
 * result.
 * 
 * @author Tomas Marek
 * 
 */
public class TransformationDictionary {

	private String fieldName;
	private Set<String> catNames;

	/**
	 * @return the fieldName
	 */
	public String getFieldName() {
		return fieldName;
	}

	/**
	 * @param fieldName
	 *            the fieldName to set
	 */
	public void setFieldName(String fieldName) {
		this.fieldName = fieldName;
	}

	/**
	 * @return the catNames
	 */
	public Set<String> getCatNames() {
		return catNames;
	}

	/**
	 * @param catNames
	 *            the catNames to set
	 */
	public void setCatNames(Set<String> catNames) {
		this.catNames = catNames;
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<TransformationDictionary>");
		ret.append("<FieldName>" + fieldName + "</FieldName>");
		for (String catName : catNames) {
			ret.append("<CatName>" + StringEscapeUtils.escapeXml(catName) + "</CatName>");
		}
		ret.append("</TransformationDictionary>");
		return ret.toString();
	}

}
