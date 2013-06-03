package xquerysearch.domain.arbquery;

import java.util.List;

/**
 * Domain object representing InterestMeasureSetting element from ARBuilder
 * query.
 * 
 * @author Tomas Marek
 * 
 */
public class InterestMeasureSetting {

	List<InterestMeasureThreshold> imThresholds;

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

}
