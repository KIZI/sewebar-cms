package xquerysearch.domain;

/**
 * Domain object representing {@link Cluster}'s centroid.
 * 
 * @author Tomas Marek
 * 
 */
public class Centroid {

	private double[][] vector;

	/**
	 * @return the vector
	 */
	public double[][] getVector() {
		return vector;
	}

	/**
	 * @param vector
	 *            the vector to set
	 */
	public void setVector(double[][] vector) {
		this.vector = vector;
	}
	
	/**
	 * {@inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("Centroid [");
		for (int i = 0; i < vector.length; i++) {
			ret.append("[");
			for (int j = 0; j < vector[i].length; j++) {
				if (j > 0) {
					ret.append(", ");
				}
				ret.append(vector[i][j]);
			}
			ret.append("]");
		}
		ret.append("]");
		return ret.toString();
	}
}
