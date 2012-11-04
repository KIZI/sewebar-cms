package xquerysearch.domain.arbquery;

/**
 * Domain object representing InterestMeasureThreshold from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class InterestMeasureThreshold {

	private String id;
	private String interestMeasure;
	private String compareType;
	private double significanceLevel;

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
	 * @return the interestMeasure
	 */
	public String getInterestMeasure() {
		return interestMeasure;
	}

	/**
	 * @param interestMeasure
	 *            the interestMeasure to set
	 */
	public void setInterestMeasure(String interestMeasure) {
		this.interestMeasure = interestMeasure;
	}

	/**
	 * @return the compareType
	 */
	public String getCompareType() {
		return compareType;
	}

	/**
	 * @param compareType
	 *            the compareType to set
	 */
	public void setCompareType(String compareType) {
		this.compareType = compareType;
	}

	/**
	 * @return the significanceLevel
	 */
	public double getSignificanceLevel() {
		return significanceLevel;
	}

	/**
	 * @param significanceLevel
	 *            the significanceLevel to set
	 */
	public void setSignificanceLevel(double significanceLevel) {
		this.significanceLevel = significanceLevel;
	}

}
