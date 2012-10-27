package xquerysearch.domain.result;

import java.util.List;

/**
 * Domain object representing DBA element from query result.
 * 
 * @author Tomas Marek
 * 
 */
public class DBA {

	private String connective;
	private List<DBA> dbas;
	private List<BBA> bbas;

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

	/**
	 * @return the bbas
	 */
	public List<BBA> getBbas() {
		return bbas;
	}

	/**
	 * @param bbas
	 *            the bbas to set
	 */
	public void setBbas(List<BBA> bbas) {
		this.bbas = bbas;
	}

	/**
	 * @{inheritDoc
	 */
	@Override
	public String toString() {
		StringBuffer ret = new StringBuffer();
		ret.append("<DBA connective=\"" + connective + "\">");
		if (bbas != null) {
			for (BBA bba : bbas) {
				ret.append(bba.toString());
			}
		} else if (dbas != null) {
			for (DBA dba : dbas) {
				ret.append(dba.toString());
			}
		}
		ret.append("</DBA>");
		return ret.toString();
	}
}
