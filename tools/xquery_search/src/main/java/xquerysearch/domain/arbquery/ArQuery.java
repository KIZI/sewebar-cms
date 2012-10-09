package xquerysearch.domain.arbquery;

/**
 * Domain object representing ARQuery Element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class ArQuery {

	private BbaSettings bbaSettings;
	private DbaSettings dbaSettings;
	private String antecedentSetting;
	private String consequentSetting;
	private String conditionSetting;
	private InterestMeasureSetting interestMeasureSetting;

	/**
	 * @return the bbaSettings
	 */
	public BbaSettings getBbaSettings() {
		return bbaSettings;
	}

	/**
	 * @param bbaSettings
	 *            the bbaSettings to set
	 */
	public void setBbaSettings(BbaSettings bbaSettings) {
		this.bbaSettings = bbaSettings;
	}

	/**
	 * @return the dbaSettings
	 */
	public DbaSettings getDbaSettings() {
		return dbaSettings;
	}

	/**
	 * @param dbaSettings
	 *            the dbaSettings to set
	 */
	public void setDbaSettings(DbaSettings dbaSettings) {
		this.dbaSettings = dbaSettings;
	}

	/**
	 * @return the antecedentSetting
	 */
	public String getAntecedentSetting() {
		return antecedentSetting;
	}

	/**
	 * @param antecedentSetting
	 *            the antecedentSetting to set
	 */
	public void setAntecedentSetting(String antecedentSetting) {
		this.antecedentSetting = antecedentSetting;
	}

	/**
	 * @return the consequentSetting
	 */
	public String getConsequentSetting() {
		return consequentSetting;
	}

	/**
	 * @param consequentSetting
	 *            the consequentSetting to set
	 */
	public void setConsequentSetting(String consequentSetting) {
		this.consequentSetting = consequentSetting;
	}

	/**
	 * @return the conditionSetting
	 */
	public String getConditionSetting() {
		return conditionSetting;
	}

	/**
	 * @param conditionSetting
	 *            the conditionSetting to set
	 */
	public void setConditionSetting(String conditionSetting) {
		this.conditionSetting = conditionSetting;
	}

	/**
	 * @return the interestMeasureSetting
	 */
	public InterestMeasureSetting getInterestMeasureSetting() {
		return interestMeasureSetting;
	}

	/**
	 * @param interestMeasureSetting
	 *            the interestMeasureSetting to set
	 */
	public void setInterestMeasureSetting(InterestMeasureSetting interestMeasureSetting) {
		this.interestMeasureSetting = interestMeasureSetting;
	}

}
