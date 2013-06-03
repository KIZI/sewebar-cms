package izi_repository.domain.result.tasksetting;

/**
 * Domain object representing ConditionSetting element from TaskSetting from
 * query result.
 * 
 * @author Tomas Marek
 * 
 */
public class ConditionSetting extends CedentSetting {

	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<ConditionSetting>");
		for (DBASetting dbaSetting : getDbaSettings()) {
			ret.append(dbaSetting.toString());
		}
		ret.append("</ConditionSetting>");
		return ret.toString();
	}
}
