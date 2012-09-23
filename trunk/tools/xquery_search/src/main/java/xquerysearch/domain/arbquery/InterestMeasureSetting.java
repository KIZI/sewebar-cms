package xquerysearch.domain.arbquery;

import java.util.Set;

/**
 * Domain object representing InterestMeasureSetting element from ARBuilder
 * query.
 * 
 * @author Tomas Marek
 * 
 */
public class InterestMeasureSetting {

	Set<InterestMeasureThreshold> imThresholds;

	/**
	 * @return the imThresholds
	 */
	public Set<InterestMeasureThreshold> getImThresholds() {
		return imThresholds;
	}

	/**
	 * @param imThresholds
	 *            the imThresholds to set
	 */
	public void setImThresholds(Set<InterestMeasureThreshold> imThresholds) {
		this.imThresholds = imThresholds;
	}

}
