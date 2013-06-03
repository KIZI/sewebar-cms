package izi_repository.domain.result;

/**
 * Domain object representing Antecedent element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class Antecedent extends Cedent {

	/**
	 * @{inheritDoc}
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<Antecedent>");
		for (DBA dba : getDbas()) {
			ret.append(dba.toString());
		}
		ret.append("</Antecedent>");
		return ret.toString();
	}
}
