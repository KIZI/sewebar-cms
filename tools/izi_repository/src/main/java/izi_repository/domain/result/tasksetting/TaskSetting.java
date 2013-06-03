package izi_repository.domain.result.tasksetting;


/**
 * Domain object representing TaskSetting element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class TaskSetting {

	private Extension extension;
	private CedentSetting antecedentSetting;
	private CedentSetting consequentSetting;
	private CedentSetting conditionSetting;
	private InterestMeasureSetting imSetting;

	/**
	 * @return the extension
	 */
	public Extension getExtension() {
		return extension;
	}

	/**
	 * @param extension
	 *            the extension to set
	 */
	public void setExtension(Extension extension) {
		this.extension = extension;
	}

	/**
	 * @return the antecedentSetting
	 */
	public CedentSetting getAntecedentSetting() {
		return antecedentSetting;
	}

	/**
	 * @param antecedentSetting
	 *            the antecedentSetting to set
	 */
	public void setAntecedentSetting(CedentSetting antecedentSetting) {
		this.antecedentSetting = antecedentSetting;
	}

	/**
	 * @return the consequentSetting
	 */
	public CedentSetting getConsequentSetting() {
		return consequentSetting;
	}

	/**
	 * @param consequentSetting
	 *            the consequentSetting to set
	 */
	public void setConsequentSetting(CedentSetting consequentSetting) {
		this.consequentSetting = consequentSetting;
	}

	/**
	 * @return the conditionSetting
	 */
	public CedentSetting getConditionSetting() {
		return conditionSetting;
	}

	/**
	 * @param conditionSetting
	 *            the conditionSetting to set
	 */
	public void setConditionSetting(CedentSetting conditionSetting) {
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

	/**
	 * @{inheritDoc
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<TaskSetting>");
		// ret.append(extension.toString());
		if (antecedentSetting != null) {
			ret.append(antecedentSetting.toString());
		}
		if (consequentSetting != null) {
			ret.append(consequentSetting.toString());
		}
		if (conditionSetting != null) {
			ret.append(conditionSetting.toString());
		}
		if (imSetting != null) {
			ret.append(imSetting.toString());
		}
		ret.append("</TaskSetting>");
		return ret.toString();
	}

}
