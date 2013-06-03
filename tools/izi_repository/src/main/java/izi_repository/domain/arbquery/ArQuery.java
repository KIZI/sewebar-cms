package izi_repository.domain.arbquery;

import java.util.List;

/**
 * Domain object representing ARQuery Element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class ArQuery {

	private QuerySettings querySettings;
	private List<BbaSetting> bbaSettings;
	private List<DbaSetting> dbaSettings;
	private String antecedentSetting;
	private String consequentSetting;
	private String conditionSetting;
	private InterestMeasureSetting interestMeasureSetting;

	/**
	 * @return the querySettings
	 */
	public QuerySettings getQuerySettings() {
		return querySettings;
	}

	/**
	 * @param querySettings
	 *            the querySettings to set
	 */
	public void setQuerySettings(QuerySettings querySettings) {
		this.querySettings = querySettings;
	}

	/**
	 * @return the bbaSettings
	 */
	public List<BbaSetting> getBbaSettings() {
		return bbaSettings;
	}

	/**
	 * @param bbaSettings
	 *            the bbaSettings to set
	 */
	public void setBbaSettings(List<BbaSetting> bbaSettings) {
		this.bbaSettings = bbaSettings;
	}

	/**
	 * @return the dbaSettings
	 */
	public List<DbaSetting> getDbaSettings() {
		return dbaSettings;
	}

	/**
	 * @param dbaSettings
	 *            the dbaSettings to set
	 */
	public void setDbaSettings(List<DbaSetting> dbaSettings) {
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
