package xquerysearch.domain;

import java.util.List;
import java.util.Map;

import xquerysearch.domain.result.Result;

/**
 * Domain object representing cluster.
 * 
 * @author Tomas Marek
 * 
 */
public class Cluster {

	private long id;
	private Map<String, Long> characteristics;
	private List<Result> results;

	/**
	 * @return the id
	 */
	public long getId() {
		return id;
	}

	/**
	 * @param id
	 *            the id to set
	 */
	public void setId(long id) {
		this.id = id;
	}

	/**
	 * @return the characteristics
	 */
	public Map<String, Long> getCharacteristics() {
		return characteristics;
	}

	/**
	 * @param characteristics
	 *            the characteristics to set
	 */
	public void setCharacteristics(Map<String, Long> characteristics) {
		this.characteristics = characteristics;
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
