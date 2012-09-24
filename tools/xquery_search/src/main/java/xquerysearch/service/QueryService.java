package xquerysearch.service;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import xquerysearch.dao.ResultsDao;
import xquerysearch.domain.Query;
import xquerysearch.domain.result.ResultSet;
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
	 * @return {@link ResultSet} or <code>null</code> when no results were found
	 */
	public ResultSet getResultSet(Query query) {
		ByteArrayOutputStream preparedQuery = QueryUtils.queryPrepare(query.getQueryBody());
		String xpath = QueryUtils.makeXPath(new ByteArrayInputStream(preparedQuery.toByteArray()), false, "sewebar1.dbxml");
        String xquery = "" +
        		"for $ar in subsequence(" + xpath + ", 1, " + 100 + ")"
                + "\n return"
                + "\n <Hit docID=\"{$ar/parent::node()/@joomlaID}\" ruleID=\"{$ar/@id}\" docName=\"{base-uri($ar)}\" reportURI=\"{$ar/parent::node()/@reportURI}\" database=\"{$ar/parent::node()/@database}\" table=\"{$ar/parent::node()/@table}\">"
                    + "\n {$ar/Text}"
                    + "<Detail>{$ar/child::node() except $ar/Text}</Detail>"
                + "\n </Hit>" +
            "";
		return dao.getResultSetByXpath(xquery);
	}

}
