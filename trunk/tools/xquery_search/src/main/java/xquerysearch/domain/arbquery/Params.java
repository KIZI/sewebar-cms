package xquerysearch.domain.arbquery;

/**
 * Domain object representing Params element from Query settings.
 * 
 * @author Tomas Marek
 * 
 */
public class Params {
	private String groupBy;
	private String fieldRef;
	private double clusterBelongingLimit;
	private String clusterDistanceFormula;

	/**
	 * @return the groupBy
	 */
	public String getGroupBy() {
		return groupBy;
	}

	/**
	 * @param groupBy
	 *            the groupBy to set
	 */
	public void setGroupBy(String groupBy) {
		this.groupBy = groupBy;
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
	 * @return the clusterBelongingLimit
	 */
	public double getClusterBelongingLimit() {
		return clusterBelongingLimit;
	}

	/**
	 * @param clusterBelongingLimit
	 *            the clusterBelongingLimit to set
	 */
	public void setClusterBelongingLimit(double clusterBelongingLimit) {
		this.clusterBelongingLimit = clusterBelongingLimit;
	}

	/**
	 * @return the clusterDistanceFormula
	 */
	public String getClusterDistanceFormula() {
		return clusterDistanceFormula;
	}

	/**
	 * @param clusterDistanceFormula
	 *            the clusterDistanceFormula to set
	 */
	public void setClusterDistanceFormula(String clusterDistanceFormula) {
		this.clusterDistanceFormula = clusterDistanceFormula;
	}

}
