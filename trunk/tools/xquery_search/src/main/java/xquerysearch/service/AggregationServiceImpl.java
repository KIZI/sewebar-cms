package xquerysearch.service;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import xquerysearch.dao.AggregationDao;

/**
 * Implementation of {@link AggregationService}.
 * 
 * @author Tomas Marek
 *
 */
@Service
public class AggregationServiceImpl extends AbstractService implements AggregationService {
	
	@Autowired
	private AggregationDao dao;
	
	/**
	 * {@inheritDoc}
	 */
	public List<String> getAllDocumentsNames() {
		return dao.getAllDocumentsNames();
	}

	/**
	 * {@inheritDoc}
	 */
	public List<String[]> getAllIndexes() {
		return dao.getAllIndexes();
	}
}
