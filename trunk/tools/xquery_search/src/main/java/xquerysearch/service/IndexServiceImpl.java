package xquerysearch.service;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import xquerysearch.dao.IndexDao;

/**
 * Implementation of {@link IndexService}.
 * 
 * @author Tomas Marek
 *
 */
@Service
public class IndexServiceImpl extends AbstractService implements IndexService {
	
	@Autowired
	private IndexDao dao;

	/**
	 * @{inheritDoc}
	 */
	@Override
	public boolean insertIndex(String index) {
		return dao.insertIndex(index);
	}
	
	/**
	 * @{inheritDoc}
	 */
	@Override
	public boolean removeIndex(String index) {
		return dao.removeIndex(index);
	}

}
