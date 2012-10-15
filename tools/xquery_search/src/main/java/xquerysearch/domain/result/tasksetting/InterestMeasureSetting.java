package xquerysearch.domain.result.tasksetting;

import java.util.List;

/**
 * Domain object representing InterestMeasureSetting element from TaskSetting
 * from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class InterestMeasureSetting {

	private List<InterestMeasureThreshold> imThresholds;

	/**
	 * @return the imThresholds
	 */
	public List<InterestMeasureThreshold> getImThresholds() {
		return imThresholds;
	}

	/**
	 * @param imThresholds
	 *            the imThresholds to set
	 */
	public void setImThresholds(List<InterestMeasureThreshold> imThresholds) {
		this.imThresholds = imThresholds;
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<InterestMeasureSetting>");
		for (InterestMeasureThreshold threshold : imThresholds) {
			ret.append(threshold.toString());
		}
		ret.append("</InterestMeasureSetting>");
		return ret.toString();
	}
}
