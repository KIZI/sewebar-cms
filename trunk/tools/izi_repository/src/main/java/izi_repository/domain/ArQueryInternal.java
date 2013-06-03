package izi_repository.domain;

import izi_repository.domain.arbquery.ArQuery;
import izi_repository.domain.arbquery.BbaSetting;
import izi_repository.domain.arbquery.InterestMeasureThreshold;
import izi_repository.domain.arbquery.datadescription.DataDescription;

import java.util.List;


/**
 * Domain object representing {@link ArQuery} used for internal purposes.
 * 
 * @author Tomas Marek
 * 
 */
public class ArQueryInternal {

	private List<BbaSetting> antecedentBbaSettingList;
	private List<BbaSetting> consequentBbaSettingList;
	private List<BbaSetting> conditionBbaSettingList;
	private List<InterestMeasureThreshold> imThresholdList;
	private DataDescription dataDescription;

	/**
	 * @return the antecedentBbaSettingList
	 */
	public List<BbaSetting> getAntecedentBbaSettingList() {
		return antecedentBbaSettingList;
	}

	/**
	 * @param antecedentBbaSettingList
	 *            the antecedentBbaSettingList to set
	 */
	public void setAntecedentBbaSettingList(List<BbaSetting> antecedentBbaSettingList) {
		this.antecedentBbaSettingList = antecedentBbaSettingList;
	}

	/**
	 * @return the consequentBbaSettingList
	 */
	public List<BbaSetting> getConsequentBbaSettingList() {
		return consequentBbaSettingList;
	}

	/**
	 * @param consequentBbaSettingList
	 *            the consequentBbaSettingList to set
	 */
	public void setConsequentBbaSettingList(List<BbaSetting> consequentBbaSettingList) {
		this.consequentBbaSettingList = consequentBbaSettingList;
	}

	/**
	 * @return the conditionBbaSettingList
	 */
	public List<BbaSetting> getConditionBbaSettingList() {
		return conditionBbaSettingList;
	}

	/**
	 * @param conditionBbaSettingList
	 *            the conditionBbaSettingList to set
	 */
	public void setConditionBbaSettingList(List<BbaSetting> conditionBbaSettingList) {
		this.conditionBbaSettingList = conditionBbaSettingList;
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

	/**
	 * @return the dataDescription
	 */
	public DataDescription getDataDescription() {
		return dataDescription;
	}

	/**
	 * @param dataDescription
	 *            the dataDescription to set
	 */
	public void setDataDescription(DataDescription dataDescription) {
		this.dataDescription = dataDescription;
	}

}
