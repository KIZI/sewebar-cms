package xquerysearch.domain.arbquery;

import xquerysearch.domain.arbquery.datadescription.DataDescription;


/**
 * Domain object representing Query from ARBuilder.
 * 
 * @author Tomas Marek
 * 
 */
public class ArBuilderQuery {

	private DataDescription dataDescription;
	private ArQuery arQuery;

	/**
	 * @return the arQuery
	 */
	public ArQuery getArQuery() {
		return arQuery;
	}

	/**
	 * @param arQuery
	 *            the arQuery to set
	 */
	public void setArQuery(ArQuery arQuery) {
		this.arQuery = arQuery;
	}

	/**
	 * @return the dataDescription
	 */
	public DataDescription getDataDescription() {
		return dataDescription;
	}

	/**
	 * @param dataDescription
	 *            the dataDescription to set
	 */
	public void setDataDescription(DataDescription dataDescription) {
		this.dataDescription = dataDescription;
	}

}
