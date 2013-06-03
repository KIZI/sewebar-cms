package izi_repository.domain.result.tasksetting;

import java.util.List;

/**
 * Domain object representing cedent setting (antecedent, consequent, condition)
 * from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class CedentSetting {

	private List<DBASetting> dbaSettings;

	/**
	 * @return the dbaSettings
	 */
	public List<DBASetting> getDbaSettings() {
		return dbaSettings;
	}

	/**
	 * @param dbaSettings
	 *            the dbaSettings to set
	 */
	public void setDbaSettings(List<DBASetting> dbaSettings) {
		this.dbaSettings = dbaSettings;
	}

}
