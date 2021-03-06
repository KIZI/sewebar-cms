package izi_repository.dao;

import java.util.List;

/**
 * DAO for stored queries.
 * 
 * @author Tomas Marek
 *
 */
public interface StoredQueryDao {

	public String getQueryById(String queryId);
	
	public boolean insertQuery(String queryId, String queryBody);
	
	public boolean removeQuery(String queryId);
	
	public List<String> getNames();
}
