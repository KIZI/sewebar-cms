package xquerysearch.domain.arbquery.hybridquery;

import xquerysearch.domain.arbquery.ArQuery;
import xquerysearch.domain.arbquery.datadescription.DataDescription;
import xquerysearch.domain.arbquery.tasksetting.ArTsQuery;

/**
 * Domain object representing AR Builder query containing both AssociationRule
 * and TaskSetting querying information.
 * 
 * @author Tomas Marek
 * 
 */
public class ArHybridBuilderQuery {

	private DataDescription dataDescription;
	private ArQuery arQuery;
	private ArTsQuery arTsQuery;

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

}
