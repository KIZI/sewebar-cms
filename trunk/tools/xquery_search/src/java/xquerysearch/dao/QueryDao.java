package xquerysearch.dao;

import java.util.List;

/**
 * DAO for stored queries.
 * 
 * @author Tomas Marek
 *
 */
public interface QueryDao {

	public String getQueryById(String queryId);
	
	public boolean insertQuery(String queryId, String queryBody);
	
	public boolean removeQuery(String queryId);
	
	public List<String> getNames();
}
