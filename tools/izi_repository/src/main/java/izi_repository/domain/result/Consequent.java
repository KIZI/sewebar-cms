package izi_repository.domain.result;

/**
 * Domain object representing Consequent element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class Consequent extends Cedent {

	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<Consequent>");
		for (DBA dba : getDbas()) {
			ret.append(dba.toString());
		}
		ret.append("</Consequent>");
		return ret.toString();
	}
}
