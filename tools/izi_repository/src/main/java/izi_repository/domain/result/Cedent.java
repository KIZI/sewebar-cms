package izi_repository.domain.result;

import java.util.List;

/**
 * Domain object representing Antecedent, consequent or condition element from
 * query result.
 * 
 * @author Tomas Marek
 * 
 */
public class Cedent {

	private List<DBA> dbas;

	/**
	 * @return the dbas
	 */
	public List<DBA> getDbas() {
		return dbas;
	}

	/**
	 * @param dbas
	 *            the dbas to set
	 */
	public void setDbas(List<DBA> dbas) {
		this.dbas = dbas;
	}

}
