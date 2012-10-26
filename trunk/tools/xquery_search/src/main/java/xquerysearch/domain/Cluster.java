package xquerysearch.domain;

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
	private List<Result> results;

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
	 * @param results
	 *            the results to set
	 */
	public void setResults(List<Result> results) {
		this.results = results;
	}

}
