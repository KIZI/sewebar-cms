package xquerysearch.service;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import xquerysearch.dao.AggregationDao;

/**
 * Provides additional informations.
 * 
 * @author Tomas Marek
 *
 */
@Service
public class AggregationService {
	
	@Autowired
	private AggregationDao dao;
	
	public List<String> getAllDocumentsNames() {
		return dao.getAllDocumentsNames();
	}

	public List<String[]> getAllIndexes() {
		return dao.getAllIndexes();
	}
}
