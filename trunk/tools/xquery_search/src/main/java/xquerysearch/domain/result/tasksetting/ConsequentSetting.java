package xquerysearch.domain.result.tasksetting;

/**
 * Domain object representing ConsequentSetting element from TaskSetting from
 * query result.
 * 
 * @author Tomas Marek
 * 
 */
public class ConsequentSetting extends CedentSetting {

	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<ConsequentSetting>");
		for (DBASetting dbaSetting : getDbaSettings()) {
			ret.append(dbaSetting.toString());
		}
		ret.append("</ConsequentSetting>");
		return ret.toString();
	}
}
