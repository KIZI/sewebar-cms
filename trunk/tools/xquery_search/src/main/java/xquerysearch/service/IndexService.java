package xquerysearch.service;

import org.springframework.stereotype.Service;

import xquerysearch.dao.IndexDao;

/**
 * Provides functionality for indexes.
 * 
 * @author Tomas Marek
 *
 */
@Service
public class IndexService {
	
	IndexDao dao;
	
	public boolean insertIndex(String index) {
		return dao.insertIndex(index);
	}
	
	public boolean removeIndex(String index) {
		return dao.removeIndex(index);
	}

}
