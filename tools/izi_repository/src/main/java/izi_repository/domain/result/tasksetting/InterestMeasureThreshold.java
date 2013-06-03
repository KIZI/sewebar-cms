package izi_repository.domain.result.tasksetting;

/**
 * Domain object representing InterestMeasureThresold element from TaskSetting
 * from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class InterestMeasureThreshold {

	private String id;
	private String longName;
	private String shortName;
	private String description;
	private String order;
	private String interestMeasure;
	private String compareType;
	private String thresholdString;
	private Threshold threshold;

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
	 * @return the longName
	 */
	public String getLongName() {
		return longName;
	}

	/**
	 * @param longName
	 *            the longName to set
	 */
	public void setLongName(String longName) {
		this.longName = longName;
	}

	/**
	 * @return the shortName
	 */
	public String getShortName() {
		return shortName;
	}

	/**
	 * @param shortName
	 *            the shortName to set
	 */
	public void setShortName(String shortName) {
		this.shortName = shortName;
	}

	/**
	 * @return the description
	 */
	public String getDescription() {
		return description;
	}

	/**
	 * @param description
	 *            the description to set
	 */
	public void setDescription(String description) {
		this.description = description;
	}

	/**
	 * @return the order
	 */
	public String getOrder() {
		return order;
	}

	/**
	 * @param order
	 *            the order to set
	 */
	public void setOrder(String order) {
		this.order = order;
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
	 * @return the threshold
	 */
	public Threshold getThreshold() {
		return threshold;
	}

	/**
	 * @param threshold
	 *            the threshold to set
	 */
	public void setThreshold(Threshold threshold) {
		this.threshold = threshold;
	}

	/**
	 * @return the tresholdString
	 */
	public String getThresholdString() {
		return thresholdString;
	}

	/**
	 * @param tresholdString
	 *            the tresholdString to set
	 */
	public void setThresholdString(String thresholdString) {
		this.thresholdString = thresholdString;
	}

	/**
	 * @{inheritDoc
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<InterestMeasureThreshold id=\"" + id + "\">");
		ret.append("<Extension name=\"LongName\">" + longName + "</Extension>");
		ret.append("<Extension name=\"Description\">" + description + "</Extension>");
		ret.append("<Extension name=\"Order\">" + order + "</Extension>");
		ret.append("<InterestMeasure>" + interestMeasure + "</InterestMeasure>");
		ret.append("<CompareType>" + compareType + "</CompareType>");
		if (threshold != null) {
			ret.append(threshold.toString());
		}
		ret.append("</InterestMeasureThreshold>");
		return ret.toString();
	}
}
