package xquerysearch.service;

import java.util.List;

import org.springframework.stereotype.Service;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.Result;

/**
 * This service provides querying to database for results.
 * 
 * @author Tomas Marek
 *
 */
@Service
public class QueryService {
	
	private ResultsDao dao;
	
	/**
	 * Gets results from database by query.
	 * @param query
	 * @return list of {@link Result}s or <code>null</code> when no results were found
	 */
	public List<Result> getResults(Query query) {
		return dao.getResultsByQuery(query);
	}

}
