package xquerysearch.domain.output;

import java.util.ArrayList;
import java.util.List;

/**
 * Domain object representing DBA element in output.
 * 
 * @author Tomas Marek
 * 
 */
public class DBA {

	private String id;
	private String connective;
	private List<String> baRefs = new ArrayList<String>();

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
	 * @return the connective
	 */
	public String getConnective() {
		return connective;
	}

	/**
	 * @param connective
	 *            the connective to set
	 */
	public void setConnective(String connective) {
		this.connective = connective;
	}

	/**
	 * @return the baRefs
	 */
	public List<String> getBaRefs() {
		return baRefs;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();

		ret.append("<DBA id=\"" + id + "\" connective=\"" + connective + "\">");

		for (String baRef : baRefs) {
			ret.append("<BARef>" + baRef + "</BARef>");
		}

		ret.append("</DBA>");

		return ret.toString();
	}
}
