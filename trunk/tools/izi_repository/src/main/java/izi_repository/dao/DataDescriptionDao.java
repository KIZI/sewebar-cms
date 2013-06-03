package izi_repository.dao;

/**
 * DAO for data description.
 * 
 * @author Tomas Marek
 *
 */
public interface DataDescriptionDao {
	
	public String getDataDescriptionFromData();

	public String getDataDescriptionFromCache();
	
	public boolean saveDataDescriptionIntoCache(String dataDescription);
}
