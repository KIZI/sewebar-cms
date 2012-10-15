package xquerysearch.domain.result.tasksetting;

/**
 * Domain object representing AntecedentSetting element from TaskSetting from
 * query result.
 * 
 * @author Tomas Marek
 * 
 */
public class AntecedentSetting extends CedentSetting {

	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<AntecedentSetting>");
		for (DBASetting dbaSetting : getDbaSettings()) {
			ret.append(dbaSetting.toString());
		}
		ret.append("</AntecedentSetting>");
		return ret.toString();
	}
}
