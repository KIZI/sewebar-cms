package xquerysearch.domain.result;

import java.util.Set;

/**
 * Domain object representing Antecedent, consequent or condition element from
 * query result.
 * 
 * @author Tomas Marek
 * 
 */
public class Cedent {

	private Set<DBA> dbas;

	/**
	 * @return the dbas
	 */
	public Set<DBA> getDbas() {
		return dbas;
	}

	/**
	 * @param dbas
	 *            the dbas to set
	 */
	public void setDbas(Set<DBA> dbas) {
		this.dbas = dbas;
	}

}
