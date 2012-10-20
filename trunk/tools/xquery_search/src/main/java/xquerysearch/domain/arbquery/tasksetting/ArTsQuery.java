package xquerysearch.domain.arbquery.tasksetting;

import java.util.Set;

import xquerysearch.domain.arbquery.InterestMeasureSetting;
import xquerysearch.domain.arbquery.QuerySettings;

/**
 * Domain object representing ARQuery Element from ARBuilder query.
 * 
 * @author Tomas Marek
 * 
 */
public class ArTsQuery {

	private QuerySettings querySettings;
	private Set<BBASetting> bbaSettings;
	private Set<DBASetting> dbaSettings;
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
	public Set<BBASetting> getBbaSettings() {
		return bbaSettings;
	}

	/**
	 * @param bbaSettings
	 *            the bbaSettings to set
	 */
	public void setBbaSettings(Set<BBASetting> bbaSettings) {
		this.bbaSettings = bbaSettings;
	}

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
