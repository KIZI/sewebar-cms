package xquerysearch.domain.arbquery.tasksetting;

import xquerysearch.domain.arbquery.datadescription.DataDescription;


/**
 * Domain object representing Query from ARBuilder.
 * 
 * @author Tomas Marek
 * 
 */
public class ArTsBuilderQuery {

	private DataDescription dataDescription;
	private ArTsQuery arQuery;

	/**
	 * @return the arQuery
	 */
	public ArTsQuery getArQuery() {
		return arQuery;
	}

	/**
	 * @param arQuery
	 *            the arQuery to set
	 */
	public void setArQuery(ArTsQuery arQuery) {
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
