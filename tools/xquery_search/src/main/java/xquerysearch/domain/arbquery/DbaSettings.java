package xquerysearch.domain.arbquery;

import java.util.Set;


/**
 * @author Tomas
 * 
 */
public class DbaSettings {

	private Set<DbaSetting> dbaSettings;

	/**
	 * @return the dbaSettings
	 */
	public Set<DbaSetting> getDbaSettings() {
		return dbaSettings;
	}

	/**
	 * @param dbaSettings
	 *            the dbaSettings to set
	 */
	public void setDbaSettings(Set<DbaSetting> dbaSettings) {
		this.dbaSettings = dbaSettings;
	}

}
