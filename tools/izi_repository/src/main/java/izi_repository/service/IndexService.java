package izi_repository.service;

/**
 * Service for database indexes.
 * 
 * @author Tomas Marek
 *
 */
public interface IndexService {
	
	/**
	 * Creates new index in DB.
	 * 
	 * @param index
	 * @return
	 */
	public boolean insertIndex(String index);
	
	/**
	 * Removes index from DB.
	 * 
	 * @param index
	 * @return
	 */
	public boolean removeIndex(String index);

}
