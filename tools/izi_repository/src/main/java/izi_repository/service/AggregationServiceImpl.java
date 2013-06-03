package izi_repository.service;

import izi_repository.dao.AggregationDao;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;


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

	/**
	 * @{inheritDoc}
	 */
	@Override
	public Long getDocumentsCount() {
		return dao.getDocumentsCount();
	}

	/**
	 * @{inheritDoc}
	 */
	@Override
	public Long getAssociationRulesCount() {
		return dao.getAssociationRulesCount();
	}
}
