package xquerysearch.domain;

/**
 * Domain object representing {@link Cluster}'s centroid.
 * 
 * @author Tomas Marek
 * 
 */
public class Centroid {

	private Double[][] vector;

	/**
	 * @return the vector
	 */
	public Double[][] getVector() {
		return vector;
	}

	/**
	 * @param vector
	 *            the vector to set
	 */
	public void setVector(Double[][] vector) {
		this.vector = vector;
	}

}
