package izi_repository.domain.arbquery.tasksetting;

import izi_repository.domain.arbquery.InterestMeasureSetting;
import izi_repository.domain.arbquery.QuerySettings;

import java.util.List;


/**
 * Domain object representing ARQuery Element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class ArTsQuery {

	private QuerySettings querySettings;
	private List<BBASetting> bbaSettings;
	private List<DBASetting> dbaSettings;
	private String antecedentSetting;
	private String consequentSetting;
	private String conditionSetting;
	private InterestMeasureSetting imSetting;

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
	public List<BBASetting> getBbaSettings() {
		return bbaSettings;
	}

	/**
	 * @param bbaSettings
	 *            the bbaSettings to set
	 */
	public void setBbaSettings(List<BBASetting> bbaSettings) {
		this.bbaSettings = bbaSettings;
	}

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
	 * @return the imSetting
	 */
	public InterestMeasureSetting getImSetting() {
		return imSetting;
	}

	/**
	 * @param imSetting
	 *            the imSetting to set
	 */
	public void setImSetting(InterestMeasureSetting imSetting) {
		this.imSetting = imSetting;
	}

}
