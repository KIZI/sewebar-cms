package xquerysearch.dao;

/**
 * DAO for database index.
 * 
 * @author Tomas Marek
 *
 */
public interface IndexDao {
	
	public boolean insertIndex(String index);

	public boolean removeIndex(String index);
}
