package xquerysearch.domain;

import java.util.ArrayList;
import java.util.List;

import xquerysearch.domain.result.Result;

/**
 * Domain object representing cluster.
 * 
 * @author Tomas Marek
 * 
 */
public class Cluster {

	private Centroid centroid;
	private List<Result> results = new ArrayList<Result>();

	/**
	 * @return the centroid
	 */
	public Centroid getCentroid() {
		return centroid;
	}

	/**
	 * @param centroid
	 *            the centroid to set
	 */
	public void setCentroid(Centroid centroid) {
		this.centroid = centroid;
	}

	/**
	 * @return the results
	 */
	public List<Result> getResults() {
		return results;
	}

	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<Cluster centroid=\"" + centroid.toString() + "\" hitcount=\"" + results.size() + "\">");
		for (Result result : results) {
			ret.append(result.toString());
		}
		ret.append("</Cluster>");
		return ret.toString();
	}
}
