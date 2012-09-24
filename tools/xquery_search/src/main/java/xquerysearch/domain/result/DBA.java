package xquerysearch.domain.result;

import java.util.Set;

/**
 * Domain object representing DBA element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class DBA {

	private String connective;
	private Set<DBA> dbas;
	private BBA bba;

	/**
	 * @return the connective
	 */
	public String getConnective() {
		return connective;
	}

	/**
	 * @param connective
	 *            the connective to set
	 */
	public void setConnective(String connective) {
		this.connective = connective;
	}

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

	/**
	 * @return the bba
	 */
	public BBA getBba() {
		return bba;
	}

	/**
	 * @param bba
	 *            the bba to set
	 */
	public void setBba(BBA bba) {
		this.bba = bba;
	}

}
