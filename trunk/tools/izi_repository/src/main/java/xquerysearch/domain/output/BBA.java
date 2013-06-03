package xquerysearch.domain.output;

import java.util.ArrayList;
import java.util.List;

import org.apache.commons.lang.StringEscapeUtils;

/**
 * Domain object representing BBA element in output.
 * 
 * @author Tomas Marek
 * 
 */
public class BBA {

	private String id;
	private String text;
	private String fieldRef;
	private List<String> catRefs = new ArrayList<String>();

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
	 * @return the text
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
	 * @return the catRefs
	 */
	public List<String> getCatRefs() {
		return catRefs;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		
		ret.append("<BBA id=\"" + id + "\">");
		ret.append("<Text>" + text + "</Text>");
		ret.append("<FieldRef>" + fieldRef + "</FieldRef>");
		
		for (String category : catRefs) {
			ret.append("<CatRef>" + StringEscapeUtils.escapeXml(category) + "</CatRef>");
		}
		
		ret.append("</BBA>");
		return ret.toString();
	}
}
