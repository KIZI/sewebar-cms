package xquerysearch.domain.result.tasksetting;

import java.util.Set;

/**
 * Domain object representing cedent setting (antecedent, consequent, condition)
 * from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class CedentSetting {

	private Set<DBASetting> dbaSettings;

	/**
	 * @return the dbaSettings
	 */
	public Set<DBASetting> getDbaSettings() {
		return dbaSettings;
	}

	/**
	 * @param dbaSettings
	 *            the dbaSettings to set
	 */
	public void setDbaSettings(Set<DBASetting> dbaSettings) {
		this.dbaSettings = dbaSettings;
	}

}
