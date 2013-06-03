package izi_repository.domain.arbquery.tasksetting;

import izi_repository.domain.arbquery.datadescription.DataDescription;


/**
 * Domain object representing Query from ARBuilder.
 * 
 * @author Tomas Marek
 * 
 */
public class ArTsBuilderQuery {

	private DataDescription dataDescription;
	private ArTsQuery arTsQuery;

	/**
	 * @return the arTsQuery
	 */
	public ArTsQuery getArTsQuery() {
		return arTsQuery;
	}

	/**
	 * @param arTsQuery
	 *            the arTsQuery to set
	 */
	public void setArTsQuery(ArTsQuery arTsQuery) {
		this.arTsQuery = arTsQuery;
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
