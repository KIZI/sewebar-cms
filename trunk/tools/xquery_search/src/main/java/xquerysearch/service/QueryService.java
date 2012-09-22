package xquerysearch.service;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.Result;
import xquerysearch.utils.QueryUtils;

/**
 * This service provides querying to database for results.
 * 
 * @author Tomas Marek
 *
 */
@Service
public class QueryService {
	
	@Autowired
	private ResultsDao dao;
	
	/**
	 * Gets results from database by query.
	 * @param query
	 * @return list of {@link Result}s or <code>null</code> when no results were found
	 */
	public List<Result> getResults(Query query) {
		ByteArrayOutputStream preparedQuery = QueryUtils.queryPrepare(query.getQueryBody());
		String xpath = QueryUtils.makeXPath(new ByteArrayInputStream(preparedQuery.toByteArray()), false, "sewebar1.dbxml");
		return dao.getResultsByXpath(xpath);
	}

}
