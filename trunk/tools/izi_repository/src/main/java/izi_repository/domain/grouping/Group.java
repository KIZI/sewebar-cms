package izi_repository.domain.grouping;

import izi_repository.domain.result.Result;

import java.util.ArrayList;
import java.util.List;


/**
 * Domain object representing group.
 * 
 * @author Tomas Marek
 * 
 */
public class Group {

	private List<Result> results = new ArrayList<Result>();
	private GroupDescription description;

	/**
	 * @return the results
	 */
	public List<Result> getResults() {
		return results;
	}

	/**
	 * @return the description
	 */
	public GroupDescription getDescription() {
		return description;
	}

	/**
	 * @param description
	 *            the description to set
	 */
	public void setDescription(GroupDescription description) {
		this.description = description;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<Group description=\"" + description + "\" hitcount=\"" + results.size() + "\">");
		for (Result result : results) {
			ret.append(result.toString());
		}
		ret.append("</Group>");
		return ret.toString();
	}
}
