package xquerysearch.domain.result;

/**
 * Domain object representing Condition element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class Condition extends Cedent {

	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<Condition>");
		for (DBA dba : getDbas()) {
			ret.append(dba.toString());
		}
		ret.append("</Condition>");
		return ret.toString();
	}
}
