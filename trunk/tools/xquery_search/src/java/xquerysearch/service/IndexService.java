package xquerysearch.service;

import xquerysearch.dao.IndexDao;

/**
 * Provides functionality for indexes.
 * 
 * @author Tomas Marek
 *
 */
public class IndexService {
	
	IndexDao dao;
	
	public boolean insertIndex(String index) {
		return dao.insertIndex(index);
	}
	
	public boolean removeIndex(String index) {
		return dao.removeIndex(index);
	}

}
