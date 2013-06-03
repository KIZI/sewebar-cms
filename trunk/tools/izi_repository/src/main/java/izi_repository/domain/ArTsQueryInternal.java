package izi_repository.domain;

import izi_repository.domain.arbquery.InterestMeasureThreshold;
import izi_repository.domain.arbquery.tasksetting.BBASetting;

import java.util.List;


/**
 * Domain object representing TaskSetting query for internal purposes.
 * 
 * @author Tomas Marek
 * 
 */
public class ArTsQueryInternal {

	private List<BBASetting> antecedentBbaSettings;
	private List<BBASetting> consequentBbaSettings;
	private List<BBASetting> conditionBbaSettings;
	private List<InterestMeasureThreshold> imThresholdList;

	/**
	 * @return the antecedentBbaSettings
	 */
	public List<BBASetting> getAntecedentBbaSettings() {
		return antecedentBbaSettings;
	}

	/**
	 * @param antecedentBbaSettings
	 *            the antecedentBbaSettings to set
	 */
	public void setAntecedentBbaSettings(List<BBASetting> antecedentBbaSettings) {
		this.antecedentBbaSettings = antecedentBbaSettings;
	}

	/**
	 * @return the consequentBbaSettings
	 */
	public List<BBASetting> getConsequentBbaSettings() {
		return consequentBbaSettings;
	}

	/**
	 * @param consequentBbaSettings
	 *            the consequentBbaSettings to set
	 */
	public void setConsequentBbaSettings(List<BBASetting> consequentBbaSettings) {
		this.consequentBbaSettings = consequentBbaSettings;
	}

	/**
	 * @return the conditionBbaSettings
	 */
	public List<BBASetting> getConditionBbaSettings() {
		return conditionBbaSettings;
	}

	/**
	 * @param conditionBbaSettings
	 *            the conditionBbaSettings to set
	 */
	public void setConditionBbaSettings(List<BBASetting> conditionBbaSettings) {
		this.conditionBbaSettings = conditionBbaSettings;
	}

	/**
	 * @return the imThresholdList
	 */
	public List<InterestMeasureThreshold> getImThresholdList() {
		return imThresholdList;
	}

	/**
	 * @param imThresholdList
	 *            the imThresholdList to set
	 */
	public void setImThresholdList(List<InterestMeasureThreshold> imThresholdList) {
		this.imThresholdList = imThresholdList;
	}

}
